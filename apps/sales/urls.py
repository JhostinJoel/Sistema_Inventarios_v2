from django.urls import path
from .views import (
    ClientListView, ClientCreateView, ClientUpdateView, ClientDeleteView,
    SupplierListView, SupplierCreateView, SupplierUpdateView, SupplierDeleteView,
    SaleListView, SaleDetailView, POSView, ProcessSaleView, ReportView
)

app_name = 'sales'

urlpatterns = [
    # Clients
    path('clients/', ClientListView.as_view(), name='client_list'),
    path('clients/add/', ClientCreateView.as_view(), name='client_add'),
    path('clients/<int:pk>/edit/', ClientUpdateView.as_view(), name='client_edit'),
    path('clients/<int:pk>/delete/', ClientDeleteView.as_view(), name='client_delete'),

    # Suppliers
    path('suppliers/', SupplierListView.as_view(), name='supplier_list'),
    path('suppliers/add/', SupplierCreateView.as_view(), name='supplier_add'),
    path('suppliers/<int:pk>/edit/', SupplierUpdateView.as_view(), name='supplier_edit'),
    path('suppliers/<int:pk>/delete/', SupplierDeleteView.as_view(), name='supplier_delete'),

    # Sales
    path('sales/', SaleListView.as_view(), name='sale_list'),
    path('sales/<int:pk>/', SaleDetailView.as_view(), name='sale_detail'),
    path('reports/', ReportView.as_view(), name='sales_report'),
    path('pos/', POSView.as_view(), name='pos'),
    path('pos/process/', ProcessSaleView.as_view(), name='process_sale'),
]
