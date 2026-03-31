import React from 'react';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonList,
  IonMenuButton,
  IonButton,
  IonIcon,
} from '@ionic/react';
import { useAuth } from '../stores/AuthContext';
import { Redirect } from 'react-router-dom';

const Settings: React.FC = () => {
  const { user, logout } = useAuth();

  const handleLogout = async () => {
    await logout();
  };

  return (
    <IonPage>
      <IonHeader>
        <IonToolbar>
          <IonButtons slot="start">
            <IonMenuButton />
          </IonButtons>
          <IonTitle>Settings</IonTitle>
        </IonToolbar>
      </IonHeader>

      <IonContent>
        <div className="ion-padding">
          <div className="profile-section">
            <div className="avatar">
              {user?.name?.charAt(0).toUpperCase()}
            </div>
            <h2>{user?.name}</h2>
            <p>{user?.email}</p>
            <span className="role-badge">{user?.role}</span>
          </div>

          <div className="settings-section">
            <h3>Account</h3>
            <div className="settings-item">
              <span>Business Name</span>
              <span>{user?.tenant_id || 'N/A'}</span>
            </div>
            <div className="settings-item">
              <span>Role</span>
              <span>{user?.role}</span>
            </div>
          </div>

          <div className="settings-section">
            <h3>App Settings</h3>
            <div className="settings-item">
              <span>Notifications</span>
              <span>Enabled</span>
            </div>
            <div className="settings-item">
              <span>Sound Effects</span>
              <span>Enabled</span>
            </div>
          </div>

          <IonButton expand="block" color="danger" onClick={handleLogout}>
            Logout
          </IonButton>
        </div>
      </IonContent>
    </IonPage>
  );
};

export default Settings;
