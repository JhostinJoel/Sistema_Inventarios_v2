from django.contrib.auth.views import LoginView, LogoutView
from django.contrib.auth import login as auth_login
from django.urls import reverse_lazy
from django.contrib import messages
from django.views.generic import ListView, CreateView, UpdateView, DeleteView, TemplateView
from django.contrib.auth.mixins import LoginRequiredMixin
from .models import User, AuditLog
from .forms import UserForm, ProfileForm, PasswordChangeForm
from .permissions import AdminRequiredMixin
from .utils import log_action

class CustomLoginView(LoginView):
    template_name = 'users/login.html'
    redirect_authenticated_user = True
    
    def get_success_url(self):
        return reverse_lazy('dashboard:index')

    def form_valid(self, form):
        response = super().form_valid(form)
        log_action(
            user=self.request.user,
            action='LOGIN',
            description=f'Usuario {self.request.user.username} inició sesión',
            request=self.request
        )
        return response

    def form_invalid(self, form):
        messages.error(self.request, "Usuario o contraseña incorrectos.")
        return super().form_invalid(form)

class CustomLogoutView(LogoutView):
    http_method_names = ['get', 'post', 'options']
    next_page = 'users:login'
    
    def dispatch(self, request, *args, **kwargs):
        if request.user.is_authenticated:
            log_action(
                user=request.user,
                action='LOGOUT',
                description=f'Usuario {request.user.username} cerró sesión',
                request=request
            )
        return super().dispatch(request, *args, **kwargs)

    def get(self, request, *args, **kwargs):
        return self.post(request, *args, **kwargs)

# User Management Views (Admin only)
class UserListView(LoginRequiredMixin, AdminRequiredMixin, ListView):
    model = User
    template_name = 'users/user_list.html'
    context_object_name = 'users'
    ordering = ['-date_joined']

class UserCreateView(LoginRequiredMixin, AdminRequiredMixin, CreateView):
    model = User
    form_class = UserForm
    template_name = 'users/user_form.html'
    success_url = reverse_lazy('users:user_list')

    def form_valid(self, form):
        response = super().form_valid(form)
        log_action(
            user=self.request.user,
            action='CREATE',
            model_name='User',
            object_id=self.object.id,
            description=f'Usuario {self.object.username} creado',
            request=self.request
        )
        messages.success(self.request, "Usuario creado exitosamente.")
        return response

class UserUpdateView(LoginRequiredMixin, AdminRequiredMixin, UpdateView):
    model = User
    form_class = UserForm
    template_name = 'users/user_form.html'
    success_url = reverse_lazy('users:user_list')

    def form_valid(self, form):
        response = super().form_valid(form)
        log_action(
            user=self.request.user,
            action='UPDATE',
            model_name='User',
            object_id=self.object.id,
            description=f'Usuario {self.object.username} actualizado',
            request=self.request
        )
        messages.success(self.request, "Usuario actualizado exitosamente.")
        return response

class UserDeleteView(LoginRequiredMixin, AdminRequiredMixin, DeleteView):
    model = User
    template_name = 'users/user_confirm_delete.html'
    success_url = reverse_lazy('users:user_list')

    def delete(self, request, *args, **kwargs):
        user_to_delete = self.get_object()
        log_action(
            user=request.user,
            action='DELETE',
            model_name='User',
            object_id=user_to_delete.id,
            description=f'Usuario {user_to_delete.username} eliminado',
            request=request
        )
        messages.success(self.request, "Usuario eliminado exitosamente.")
        return super().delete(request, *args, **kwargs)

# Profile Management
class ProfileView(LoginRequiredMixin, TemplateView):
    template_name = 'users/profile.html'

class ProfileUpdateView(LoginRequiredMixin, UpdateView):
    model = User
    form_class = ProfileForm
    template_name = 'users/profile_edit.html'
    success_url = reverse_lazy('users:profile')

    def get_object(self):
        return self.request.user

    def form_valid(self, form):
        response = super().form_valid(form)
        messages.success(self.request, "Perfil actualizado exitosamente.")
        return response

# Audit Log View (Admin only)
class AuditLogView(LoginRequiredMixin, AdminRequiredMixin, ListView):
    model = AuditLog
    template_name = 'users/audit_log.html'
    context_object_name = 'logs'
    paginate_by = 50
