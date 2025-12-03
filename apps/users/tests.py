from django.test import TestCase, Client
from django.contrib.auth import get_user_model
from django.urls import reverse

User = get_user_model()

class UserTests(TestCase):
    def setUp(self):
        self.admin_user = User.objects.create_user(
            username='admin', 
            password='password123', 
            role='ADMIN',
            email='admin@test.com'
        )
        self.seller_user = User.objects.create_user(
            username='seller', 
            password='password123', 
            role='SELLER',
            email='seller@test.com'
        )
        self.client = Client()

    def test_login_successful(self):
        response = self.client.post(reverse('users:login'), {
            'username': 'admin',
            'password': 'password123'
        })
        self.assertRedirects(response, reverse('dashboard:index'))

    def test_login_failed(self):
        response = self.client.post(reverse('users:login'), {
            'username': 'admin',
            'password': 'wrongpassword'
        })
        self.assertEqual(response.status_code, 200)
        self.assertContains(response, "Por favor, introduzca un nombre de usuario y clave correctos")

    def test_admin_access_user_list(self):
        self.client.force_login(self.admin_user)
        response = self.client.get(reverse('users:user_list'))
        self.assertEqual(response.status_code, 200)

    def test_seller_denied_user_list(self):
        self.client.force_login(self.seller_user)
        response = self.client.get(reverse('users:user_list'))
        self.assertRedirects(response, reverse('dashboard:index'))

    def test_profile_update(self):
        self.client.force_login(self.admin_user)
        url = reverse('users:profile_edit')
        response = self.client.post(url, {
            'first_name': 'Admin',
            'last_name': 'User',
            'email': 'newadmin@test.com',
            'phone': '1234567890'
        })
        self.admin_user.refresh_from_db()
        self.assertEqual(self.admin_user.email, 'newadmin@test.com')
