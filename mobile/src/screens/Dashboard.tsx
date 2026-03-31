import React, { useState, useEffect } from 'react';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonGrid,
  IonRow,
  IonCol,
  IonCard,
  IonCardHeader,
  IonCardTitle,
  IonCardContent,
  IonRefresher,
  IonRefresherContent,
} from '@ionic/react';
import { useAuth } from '../stores/AuthContext';
import { apiService } from '../services/api';

const Dashboard: React.FC = () => {
  const { user } = useAuth();
  const [stats, setStats] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadStats();
  }, []);

  const loadStats = async () => {
    try {
      const data = await apiService.getDashboardStats();
      setStats(data);
    } catch (error) {
      console.error('Error loading stats:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleRefresh = async (event: CustomEvent) => {
    await loadStats();
    (event.target as HTMLIonRefresherElement).complete();
  };

  return (
    <IonPage>
      <IonHeader>
        <IonToolbar>
          <IonTitle>Dashboard</IonTitle>
        </IonToolbar>
      </IonHeader>

      <IonContent>
        <IonRefresher slot="fixed" onIonRefresh={handleRefresh}>
          <IonRefresherContent></IonRefresherContent>
        </IonRefresher>

        <div className="ion-padding">
          <h2>Welcome, {user?.name}</h2>

          <IonGrid className="stats-grid">
            <IonRow>
              <IonCol size="6">
                <IonCard className="stat-card">
                  <IonCardHeader>
                    <IonCardTitle>Today's Revenue</IonCardTitle>
                  </IonCardHeader>
                  <IonCardContent>
                    <h2 className="stat-value">
                      ${stats?.todayRevenue?.toFixed(2) || '0.00'}
                    </h2>
                  </IonCardContent>
                </IonCard>
              </IonCol>
              <IonCol size="6">
                <IonCard className="stat-card">
                  <IonCardHeader>
                    <IonCardTitle>Orders</IonCardTitle>
                  </IonCardHeader>
                  <IonCardContent>
                    <h2 className="stat-value">{stats?.todayOrders || 0}</h2>
                  </IonCardContent>
                </IonCard>
              </IonCol>
            </IonRow>
            <IonRow>
              <IonCol size="6">
                <IonCard className="stat-card">
                  <IonCardHeader>
                    <IonCardTitle>Avg Order</IonCardTitle>
                  </IonCardHeader>
                  <IonCardContent>
                    <h2 className="stat-value">
                      ${stats?.avgOrderValue?.toFixed(2) || '0.00'}
                    </h2>
                  </IonCardContent>
                </IonCard>
              </IonCol>
              <IonCol size="6">
                <IonCard className="stat-card">
                  <IonCardHeader>
                    <IonCardTitle>Low Stock</IonCardTitle>
                  </IonCardHeader>
                  <IonCardContent>
                    <h2 className="stat-value">{stats?.lowStockCount || 0}</h2>
                  </IonCardContent>
                </IonCard>
              </IonCol>
            </IonRow>
          </IonGrid>
        </div>
      </IonContent>
    </IonPage>
  );
};

export default Dashboard;
