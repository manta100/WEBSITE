import React, { createContext, useContext, useState } from 'react';
import { Haptics, ImpactStyle } from '@capacitor/haptics';

export interface CartItem {
  id: string;
  name: string;
  price: number;
  quantity: number;
  maxQuantity: number;
}

interface CartContextType {
  items: CartItem[];
  itemCount: number;
  subtotal: number;
  tax: number;
  total: number;
  addItem: (product: any) => void;
  removeItem: (productId: string) => void;
  updateQuantity: (productId: string, quantity: number) => void;
  clearCart: () => void;
}

const CartContext = createContext<CartContextType | undefined>(undefined);

export const CartProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [items, setItems] = useState<CartItem[]>([]);
  const taxRate = 10;

  const addItem = async (product: any) => {
    Haptics.impact({ style: ImpactStyle.Light });
    
    setItems(current => {
      const existing = current.find(item => item.id === product.id);
      if (existing) {
        if (existing.quantity < existing.maxQuantity) {
          return current.map(item =>
            item.id === product.id
              ? { ...item, quantity: item.quantity + 1 }
              : item
          );
        }
        return current;
      }
      return [...current, {
        id: product.id,
        name: product.name,
        price: parseFloat(product.price),
        quantity: 1,
        maxQuantity: product.track_inventory ? product.stock_quantity : 999,
      }];
    });
  };

  const removeItem = (productId: string) => {
    Haptics.impact({ style: ImpactStyle.Medium });
    setItems(current => current.filter(item => item.id !== productId));
  };

  const updateQuantity = async (productId: string, quantity: number) => {
    Haptics.impact({ style: ImpactStyle.Light });
    setItems(current =>
      current.map(item =>
        item.id === productId && quantity > 0 && quantity <= item.maxQuantity
          ? { ...item, quantity }
          : item
      ).filter(item => item.quantity > 0)
    );
  };

  const clearCart = () => {
    setItems([]);
  };

  const itemCount = items.reduce((sum, item) => sum + item.quantity, 0);
  const subtotal = items.reduce((sum, item) => sum + item.price * item.quantity, 0);
  const tax = subtotal * (taxRate / 100);
  const total = subtotal + tax;

  return (
    <CartContext.Provider
      value={{
        items,
        itemCount,
        subtotal,
        tax,
        total,
        addItem,
        removeItem,
        updateQuantity,
        clearCart,
      }}
    >
      {children}
    </CartContext.Provider>
  );
};

export const useCart = () => {
  const context = useContext(CartContext);
  if (!context) {
    throw new Error('useCart must be used within CartProvider');
  }
  return context;
};
