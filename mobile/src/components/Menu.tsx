import React from 'react';
import {
  IonMenu,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonList,
  IonItem,
  IonIcon,
  IonLabel,
  IonMenuToggle,
} from '@ionic/react';
import { Route, Redirect } from 'react-router-dom';
import { useAuth } from '../stores/AuthContext';

interface MenuItem {
  url: string;
  icon: string;
  label: string;
}

const Menu: React.FC = () => {
  const { user } = useAuth();

  const menuItems: MenuItem[] = [
    { url: '/dashboard', icon: 'home', label: 'Dashboard' },
    { url: '/pos', icon: 'cart', label: 'Point of Sale' },
    { url: '/products', icon: 'cube', label: 'Products' },
    { url: '/orders', icon: 'list', label: 'Orders' },
    { url: '/settings', icon: 'settings', label: 'Settings' },
  ];

  return (
    <IonMenu contentId="main" type="overlay">
      <IonHeader>
        <IonToolbar>
          <IonTitle>SaaS POS</IonTitle>
        </IonToolbar>
      </IonHeader>

      <IonContent>
        <div className="menu-header">
          <div className="user-avatar">
            {user?.name?.charAt(0).toUpperCase()}
          </div>
          <div className="user-info">
            <span className="user-name">{user?.name}</span>
            <span className="user-role">{user?.role}</span>
          </div>
        </div>

        <IonList>
          {menuItems.map((item, index) => (
            <IonMenuToggle key={index} autoHide={false}>
              <IonItem button detail routerLink={item.url} routerDirection="root">
                <IonIcon slot="start" icon={item.icon} />
                <IonLabel>{item.label}</IonLabel>
              </IonItem>
            </IonMenuToggle>
          ))}
        </IonList>
      </IonContent>
    </IonMenu>
  );
};

export default Menu;
