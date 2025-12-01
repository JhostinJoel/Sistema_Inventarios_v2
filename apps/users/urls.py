from django.urls import path
from .views import (
    CustomLoginView, CustomLogoutView,
    UserListView, UserCreateView, UserUpdateView, UserDeleteView,
    ProfileView, ProfileUpdateView, AuditLogView
)
from django.contrib.auth import views as auth_views

app_name = 'users'

urlpatterns = [
    # Authentication
    path('login/', CustomLoginView.as_view(), name='login'),
    path('logout/', CustomLogoutView.as_view(), name='logout'),
    
    # User Management (Admin only)
    path('manage/', UserListView.as_view(), name='user_list'),
    path('manage/add/', UserCreateView.as_view(), name='user_add'),
    path('manage/<int:pk>/edit/', UserUpdateView.as_view(), name='user_edit'),
    path('manage/<int:pk>/delete/', UserDeleteView.as_view(), name='user_delete'),
    
    # Profile
    path('profile/', ProfileView.as_view(), name='profile'),
    path('profile/edit/', ProfileUpdateView.as_view(), name='profile_edit'),
    
    # Audit Log (Admin only)
    path('audit-log/', AuditLogView.as_view(), name='audit_log'),

    # Password Reset
    path('password_reset/', auth_views.PasswordResetView.as_view(), name='password_reset'),
    path('password_reset/done/', auth_views.PasswordResetDoneView.as_view(), name='password_reset_done'),
    path('reset/<uidb64>/<token>/', auth_views.PasswordResetConfirmView.as_view(), name='password_reset_confirm'),
    path('reset/done/', auth_views.PasswordResetCompleteView.as_view(), name='password_reset_complete'),
]
