import React, { createContext, useContext, useState, useEffect } from 'react';
import { Storage } from '@capacitor/storage';
import { apiService } from '../services/api';

interface User {
  id: string;
  name: string;
  email: string;
  role: string;
  tenant_id: string;
}

interface AuthContextType {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  loading: boolean;
  login: (email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);
  const [token, setToken] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadStoredAuth();
  }, []);

  const loadStoredAuth = async () => {
    try {
      const storedToken = await Storage.get({ key: 'auth_token' });
      const storedUser = await Storage.get({ key: 'auth_user' });
      
      if (storedToken.value && storedUser.value) {
        setToken(storedToken.value);
        setUser(JSON.parse(storedUser.value));
        apiService.setToken(storedToken.value);
      }
    } catch (error) {
      console.error('Error loading auth:', error);
    } finally {
      setLoading(false);
    }
  };

  const login = async (email: string, password: string) => {
    const response = await apiService.login(email, password);
    setToken(response.token);
    setUser(response.user);
    await Storage.set({ key: 'auth_token', value: response.token });
    await Storage.set({ key: 'auth_user', value: JSON.stringify(response.user) });
    apiService.setToken(response.token);
  };

  const logout = async () => {
    setToken(null);
    setUser(null);
    await Storage.remove({ key: 'auth_token' });
    await Storage.remove({ key: 'auth_user' });
    apiService.setToken(null);
  };

  return (
    <AuthContext.Provider
      value={{
        user,
        token,
        isAuthenticated: !!token,
        loading,
        login,
        logout,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within AuthProvider');
  }
  return context;
};
