from django.views.generic import ListView, CreateView, UpdateView, DeleteView, TemplateView, DetailView
from django.contrib.auth.mixins import LoginRequiredMixin
from django.urls import reverse_lazy
from django.contrib import messages
from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.db import transaction
from .models import Client, Supplier, Sale, SaleDetail
from .forms import ClientForm, SupplierForm
from apps.inventory.models import Product
from decimal import Decimal
import json

# Client Views
class ClientListView(LoginRequiredMixin, ListView):
    model = Client
    template_name = 'sales/client_list.html'
    context_object_name = 'clients'

class ClientCreateView(LoginRequiredMixin, CreateView):
    model = Client
    form_class = ClientForm
    template_name = 'sales/client_form.html'
    success_url = reverse_lazy('sales:client_list')

class ClientUpdateView(LoginRequiredMixin, UpdateView):
    model = Client
    form_class = ClientForm
    template_name = 'sales/client_form.html'
    success_url = reverse_lazy('sales:client_list')

class ClientDeleteView(LoginRequiredMixin, DeleteView):
    model = Client
    template_name = 'sales/client_confirm_delete.html'
    success_url = reverse_lazy('sales:client_list')

# Supplier Views
class SupplierListView(LoginRequiredMixin, ListView):
    model = Supplier
    template_name = 'sales/supplier_list.html'
    context_object_name = 'suppliers'

class SupplierCreateView(LoginRequiredMixin, CreateView):
    model = Supplier
    form_class = SupplierForm
    template_name = 'sales/supplier_form.html'
    success_url = reverse_lazy('sales:supplier_list')

class SupplierUpdateView(LoginRequiredMixin, UpdateView):
    model = Supplier
    form_class = SupplierForm
    template_name = 'sales/supplier_form.html'
    success_url = reverse_lazy('sales:supplier_list')

class SupplierDeleteView(LoginRequiredMixin, DeleteView):
    model = Supplier
    template_name = 'sales/supplier_confirm_delete.html'
    success_url = reverse_lazy('sales:supplier_list')

# Sales Views
class SaleListView(LoginRequiredMixin, ListView):
    model = Sale
    template_name = 'sales/sale_list.html'
    context_object_name = 'sales'
    ordering = ['-date']

class SaleDetailView(LoginRequiredMixin, DetailView):
    model = Sale
    template_name = 'sales/sale_detail.html'
    context_object_name = 'sale'
    
    def get_context_data(self, **kwargs):
        context = super().get_context_data(**kwargs)
        context['sale_details'] = self.object.details.all()
        return context

class POSView(LoginRequiredMixin, TemplateView):
    template_name = 'sales/pos.html'
    
    def get_context_data(self, **kwargs):
        context = super().get_context_data(**kwargs)
        context['products'] = Product.objects.filter(stock__gt=0).select_related('category')
        context['clients'] = Client.objects.all()
        return context

@method_decorator(csrf_exempt, name='dispatch')
class ProcessSaleView(LoginRequiredMixin, TemplateView):
    def post(self, request, *args, **kwargs):
        try:
            data = json.loads(request.body)
            client_id = data.get('client_id')
            items = data.get('items', [])
            
            if not items:
                return JsonResponse({'success': False, 'error': 'No hay productos en el carrito'})
            
            # Create sale
            with transaction.atomic():
                client = Client.objects.get(id=client_id) if client_id else None
                
                # Calculate total
                total = Decimal('0.00')
                for item in items:
                    total += Decimal(str(item['price'])) * Decimal(str(item['quantity']))
                
                # Create sale
                sale = Sale.objects.create(
                    client=client,
                    user=request.user,
                    total=total
                )
                
                # Create sale details and update stock
                for item in items:
                    product = Product.objects.get(id=item['product_id'])
                    quantity = int(item['quantity'])
                    price = Decimal(str(item['price']))
                    
                    # Check stock
                    if product.stock < quantity:
                        raise ValueError(f'Stock insuficiente para {product.name}')
                    
                    # Create detail
                    SaleDetail.objects.create(
                        sale=sale,
                        product=product,
                        quantity=quantity,
                        price=price,
                        subtotal=price * quantity
                    )
                    
                    # Update stock
                    product.stock -= quantity
                    product.save()
                
                return JsonResponse({
                    'success': True,
                    'sale_id': sale.id,
                    'message': f'Venta #{sale.id} registrada exitosamente'
                })
                
        except Client.DoesNotExist:
            return JsonResponse({'success': False, 'error': 'Cliente no encontrado'})
        except Product.DoesNotExist:
            return JsonResponse({'success': False, 'error': 'Producto no encontrado'})
        except ValueError as e:
            return JsonResponse({'success': False, 'error': str(e)})
        except Exception as e:
            return JsonResponse({'success': False, 'error': f'Error al procesar la venta: {str(e)}'}, status=400)

class ReportView(LoginRequiredMixin, TemplateView):
    template_name = 'sales/report.html'
    
    def get_context_data(self, **kwargs):
        context = super().get_context_data(**kwargs)
        start_date = self.request.GET.get('start_date')
        end_date = self.request.GET.get('end_date')
        
        sales = Sale.objects.all().order_by('-date')
        
        if start_date and end_date:
            sales = sales.filter(date__date__range=[start_date, end_date])
            
        context['sales'] = sales
        context['start_date'] = start_date
        context['end_date'] = end_date
        context['total_sales_count'] = sales.count()
        context['total_income'] = sum(sale.total for sale in sales)
        
        return context
