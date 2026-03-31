import { Capacitor } from '@capacitor/core';
import { StatusBar, Style } from '@capacitor/status-bar';
import { SplashScreen } from '@capacitor/splash-screen';
import { App } from '@capacitor/app';
import React from 'react';
import { IonApp, IonRouterOutlet, IonSplitPane } from '@ionic/react';
import { IonReactRouter } from '@ionic/react-router';
import { Route, Redirect } from 'react-router-dom';

import Menu from './components/Menu';
import Login from './screens/Login';
import Dashboard from './screens/Dashboard';
import POS from './screens/POS';
import Products from './screens/Products';
import Orders from './screens/Orders';
import Settings from './screens/Settings';

import { AuthProvider, useAuth } from './stores/AuthContext';
import { CartProvider } from './stores/CartContext';

const AppRoutes: React.FC = () => {
  const { isAuthenticated, loading } = useAuth();

  if (loading) {
    return <div className="loading-screen">Loading...</div>;
  }

  return (
    <IonReactRouter>
      <IonApp>
        {isAuthenticated ? (
          <>
            <Menu />
            <IonSplitPane contentId="main" when="md">
              <IonRouterOutlet id="main">
                <Route exact path="/dashboard" component={Dashboard} />
                <Route exact path="/pos" component={POS} />
                <Route exact path="/products" component={Products} />
                <Route exact path="/orders" component={Orders} />
                <Route exact path="/settings" component={Settings} />
                <Route exact path="/">
                  <Redirect to="/dashboard" />
                </Route>
              </IonRouterOutlet>
            </IonSplitPane>
          </>
        ) : (
          <IonRouterOutlet>
            <Route exact path="/login" component={Login} />
            <Route exact path="/">
              <Redirect to="/login" />
            </Route>
          </IonRouterOutlet>
        )}
      </IonApp>
    </IonReactRouter>
  );
};

const App: React.FC = () => {
  React.useEffect(() => {
    if (Capacitor.isNativePlatform()) {
      StatusBar.setStyle({ style: Style.Light });
      StatusBar.setBackgroundColor({ color: '#3b82f6' });
      SplashScreen.hide();
    }
  }, []);

  return (
    <AuthProvider>
      <CartProvider>
        <AppRoutes />
      </CartProvider>
    </AuthProvider>
  );
};

export default App;
