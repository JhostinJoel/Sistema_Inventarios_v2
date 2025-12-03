from django.test import TestCase, Client
from django.urls import reverse
from django.contrib.auth import get_user_model
from apps.sales.models import Client as SalesClient, Sale, SaleDetail, Supplier
from apps.inventory.models import Product, Category
import json

User = get_user_model()

class SalesTests(TestCase):
    def setUp(self):
        self.user = User.objects.create_user(username='admin', password='password123', role='ADMIN')
        self.client = Client()
        self.client.force_login(self.user)
        
        self.category = Category.objects.create(name='Test Cat')
        self.product = Product.objects.create(
            code='P001',
            name='Product 1',
            price=10.00,
            cost=5.00,
            stock=100,
            category=self.category
        )
        self.customer = SalesClient.objects.create(name='John Doe', email='john@test.com')

    def test_client_create(self):
        url = reverse('sales:client_add')
        data = {
            'name': 'Jane Doe',
            'email': 'jane@test.com',
            'phone': '123456',
            'address': 'Address'
        }
        response = self.client.post(url, data)
        self.assertRedirects(response, reverse('sales:client_list'))
        self.assertTrue(SalesClient.objects.filter(name='Jane Doe').exists())

    def test_process_sale_success(self):
        url = reverse('sales:process_sale')
        data = {
            'client_id': self.customer.id,
            'items': [
                {
                    'product_id': self.product.id,
                    'quantity': 2,
                    'price': 10.00
                }
            ]
        }
        response = self.client.post(url, data, content_type='application/json')
        self.assertEqual(response.status_code, 200)
        
        # Verify sale created
        self.assertTrue(Sale.objects.exists())
        sale = Sale.objects.first()
        self.assertEqual(sale.total, 20.00)
        
        # Verify stock updated
        self.product.refresh_from_db()
        self.assertEqual(self.product.stock, 98)

    def test_process_sale_insufficient_stock(self):
        url = reverse('sales:process_sale')
        data = {
            'client_id': self.customer.id,
            'items': [
                {
                    'product_id': self.product.id,
                    'quantity': 101, # More than stock
                    'price': 10.00
                }
            ]
        }
        response = self.client.post(url, data, content_type='application/json')
        self.assertEqual(response.status_code, 200)
        self.assertFalse(response.json()['success'])
        self.assertIn('Stock insuficiente', response.json()['error'])

class SupplierTests(TestCase):
    def setUp(self):
        self.user = User.objects.create_user(username='admin', password='password123', role='ADMIN')
        self.client = Client()
        self.client.force_login(self.user)
        self.supplier = Supplier.objects.create(
            name='Test Supplier',
            contact_name='Contact',
            email='supplier@test.com',
            phone='1234567890'
        )

    def test_supplier_list(self):
        response = self.client.get(reverse('sales:supplier_list'))
        self.assertEqual(response.status_code, 200)
        self.assertContains(response, 'Test Supplier')

    def test_supplier_create(self):
        url = reverse('sales:supplier_add')
        data = {
            'name': 'New Supplier',
            'contact_name': 'New Contact',
            'email': 'new@test.com',
            'phone': '0987654321',
            'address': 'New Address'
        }
        response = self.client.post(url, data)
        self.assertRedirects(response, reverse('sales:supplier_list'))
        self.assertTrue(Supplier.objects.filter(name='New Supplier').exists())

    def test_supplier_update(self):
        url = reverse('sales:supplier_edit', args=[self.supplier.id])
        data = {
            'name': 'Updated Supplier',
            'contact_name': 'Contact',
            'email': 'supplier@test.com',
            'phone': '1234567890',
            'address': 'Address'
        }
        response = self.client.post(url, data)
        self.assertRedirects(response, reverse('sales:supplier_list'))
        self.supplier.refresh_from_db()
        self.assertEqual(self.supplier.name, 'Updated Supplier')

    def test_supplier_delete(self):
        url = reverse('sales:supplier_delete', args=[self.supplier.id])
        response = self.client.post(url)
        self.assertRedirects(response, reverse('sales:supplier_list'))
        self.assertFalse(Supplier.objects.filter(id=self.supplier.id).exists())
