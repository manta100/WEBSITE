import React, { useState, useEffect } from 'react';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonSearchbar,
  IonGrid,
  IonRow,
  IonCol,
  IonCard,
  IonCardContent,
  IonBadge,
  IonModal,
  IonButtons,
  IonButton,
  IonList,
  IonItem,
  IonLabel,
  IonThumbnail,
  IonImg,
  IonText,
} from '@ionic/react';
import { useCart } from '../stores/CartContext';
import { apiService } from '../services/api';

const POS: React.FC = () => {
  const [products, setProducts] = useState<any[]>([]);
  const [searchText, setSearchText] = useState('');
  const [loading, setLoading] = useState(true);
  const [showCart, setShowCart] = useState(false);
  const [paymentMethod, setPaymentMethod] = useState('cash');
  const [amountPaid, setAmountPaid] = useState('');
  
  const { items, itemCount, subtotal, tax, total, addItem, removeItem, updateQuantity, clearCart } = useCart();

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
    p.name.toLowerCase().includes(searchText.toLowerCase()) ||
    p.sku?.toLowerCase().includes(searchText.toLowerCase()) ||
    p.barcode?.toLowerCase().includes(searchText.toLowerCase())
  );

  const handleCheckout = async () => {
    try {
      const order = await apiService.createOrder({
        items: items.map((item) => ({
          product_id: item.id,
          quantity: item.quantity,
        })),
        payment_method: paymentMethod,
        amount_paid: parseFloat(amountPaid),
      });
      
      clearCart();
      setShowCart(false);
      setAmountPaid('');
    } catch (error) {
      console.error('Checkout error:', error);
    }
  };

  const changeAmount = parseFloat(amountPaid) - total;

  return (
    <IonPage>
      <IonHeader>
        <IonToolbar>
          <IonTitle>Point of Sale</IonTitle>
          <IonButtons slot="end">
            <IonButton onClick={() => setShowCart(true)}>
              Cart ({itemCount})
            </IonButton>
          </IonButtons>
        </IonToolbar>
      </IonHeader>

      <IonContent>
        <IonSearchbar
          value={searchText}
          onIonInput={(e) => setSearchText(e.detail.value!)}
          placeholder="Search products..."
        />

        <IonGrid>
          <IonRow>
            {filteredProducts.map((product) => (
              <IonCol size="6" key={product.id}>
                <IonCard button onClick={() => addItem(product)}>
                  <IonCardContent className="product-card">
                    <div className="product-image">
                      {product.images?.[0] ? (
                        <IonImg src={product.images[0]} />
                      ) : (
                        <div className="placeholder-image">No Image</div>
                      )}
                    </div>
                    <IonText class="product-name">{product.name}</IonText>
                    <p className="product-price">${parseFloat(product.price).toFixed(2)}</p>
                    {product.track_inventory && (
                      <IonBadge color="medium">
                        {product.stock_quantity} left
                      </IonBadge>
                    )}
                  </IonCardContent>
                </IonCard>
              </IonCol>
            ))}
          </IonRow>
        </IonGrid>

        <IonModal isOpen={showCart} onDidDismiss={() => setShowCart(false)}>
          <IonHeader>
            <IonToolbar>
              <IonTitle>Cart</IonTitle>
              <IonButtons slot="end">
                <IonButton onClick={() => setShowCart(false)}>Close</IonButton>
              </IonButtons>
            </IonToolbar>
          </IonHeader>
          <IonContent>
            <IonList>
              {items.map((item) => (
                <IonItem key={item.id}>
                  <IonLabel>
                    <h3>{item.name}</h3>
                    <p>${item.price.toFixed(2)} x {item.quantity}</p>
                  </IonLabel>
                  <IonButtons slot="end">
                    <IonButton
                      fill="outline"
                      size="small"
                      onClick={() => updateQuantity(item.id, item.quantity - 1)}
                    >
                      -
                    </IonButton>
                    <IonText>{item.quantity}</IonText>
                    <IonButton
                      fill="outline"
                      size="small"
                      onClick={() => updateQuantity(item.id, item.quantity + 1)}
                    >
                      +
                    </IonButton>
                    <IonButton color="danger" onClick={() => removeItem(item.id)}>
                      Remove
                    </IonButton>
                  </IonButtons>
                </IonItem>
              ))}
            </IonList>

            <div className="cart-summary ion-padding">
              <div className="summary-row">
                <span>Subtotal:</span>
                <span>${subtotal.toFixed(2)}</span>
              </div>
              <div className="summary-row">
                <span>Tax (10%):</span>
                <span>${tax.toFixed(2)}</span>
              </div>
              <div className="summary-row total">
                <span>Total:</span>
                <span>${total.toFixed(2)}</span>
              </div>

              <div className="payment-section ion-padding-top">
                <IonList>
                  <IonItem>
                    <IonLabel>Payment Method</IonLabel>
                    <select
                      value={paymentMethod}
                      onChange={(e) => setPaymentMethod(e.target.value)}
                      className="payment-select"
                    >
                      <option value="cash">Cash</option>
                      <option value="card">Card</option>
                    </select>
                  </IonItem>
                  <IonItem>
                    <IonLabel>Amount Paid</IonLabel>
                    <input
                      type="number"
                      value={amountPaid}
                      onChange={(e) => setAmountPaid(e.target.value)}
                      className="amount-input"
                      placeholder="0.00"
                    />
                  </IonItem>
                </IonList>

                {amountPaid && (
                  <div className="change-display">
                    <span>Change:</span>
                    <span className="change-amount">${changeAmount.toFixed(2)}</span>
                  </div>
                )}

                <IonButton
                  expand="block"
                  size="large"
                  onClick={handleCheckout}
                  disabled={items.length === 0 || !amountPaid}
                >
                  Complete Sale
                </IonButton>
              </div>
            </div>
          </IonContent>
        </IonModal>
      </IonContent>
    </IonPage>
  );
};

export default POS;
