import React, { useState, useEffect } from 'react';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonSearchbar,
  IonList,
  IonItem,
  IonLabel,
  IonBadge,
  IonFab,
  IonFabButton,
  IonIcon,
} from '@ionic/react';
import { apiService } from '../services/api';

const Products: React.FC = () => {
  const [products, setProducts] = useState<any[]>([]);
  const [searchText, setSearchText] = useState('');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadProducts();
  }, []);

  const loadProducts = async () => {
    try {
      const data = await apiService.getProducts();
      setProducts(data.data || []);
    } catch (error) {
      console.error('Error loading products:', error);
    } finally {
      setLoading(false);
    }
  };

  const filteredProducts = products.filter((p) =>
    p.name.toLowerCase().includes(searchText.toLowerCase())
  );

  return (
    <IonPage>
      <IonHeader>
        <IonToolbar>
          <IonTitle>Products</IonTitle>
        </IonToolbar>
      </IonHeader>

      <IonContent>
        <IonSearchbar
          value={searchText}
          onIonInput={(e) => setSearchText(e.detail.value!)}
          placeholder="Search products..."
        />

        <IonList>
          {filteredProducts.map((product) => (
            <IonItem key={product.id} button detail>
              <IonLabel>
                <h3>{product.name}</h3>
                <p>SKU: {product.sku || 'N/A'}</p>
                {product.category && (
                  <p>{product.category.name}</p>
                )}
              </IonLabel>
              <IonBadge slot="end" color={product.is_active ? 'success' : 'medium'}>
                {product.is_active ? 'Active' : 'Inactive'}
              </IonBadge>
              <IonLabel slot="end" class="product-price">
                ${parseFloat(product.price).toFixed(2)}
              </IonLabel>
            </IonItem>
          ))}
        </IonList>
      </IonContent>
    </IonPage>
  );
};

export default Products;
