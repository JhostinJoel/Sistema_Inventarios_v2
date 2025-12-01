import google.generativeai as genai
from django.conf import settings
from apps.inventory.models import Product
from apps.sales.models import Sale
from django.db.models import Sum

def configure_gemini():
    api_key = settings.GEMINI_API_KEY if hasattr(settings, 'GEMINI_API_KEY') else None
    if api_key:
        genai.configure(api_key=api_key)
        return True
    return False

def get_chatbot_response(user_message):
    if not configure_gemini():
        return "Error: API Key de Gemini no configurada."

    model = genai.GenerativeModel('gemini-2.0-flash')
    
    # Context building
    context = "Eres un asistente virtual para un sistema de inventario. "
    context += "Puedes responder preguntas sobre productos, stock y ventas. "
    
    # Simple RAG-like approach (fetching relevant data based on keywords)
    if "producto" in user_message.lower() or "stock" in user_message.lower():
        products = Product.objects.all()[:10] # Limit context
        product_list = ", ".join([f"{p.name} (Stock: {p.stock})" for p in products])
        context += f"Información actual de productos: {product_list}. "
    
    if "ventas" in user_message.lower():
        total_sales = Sale.objects.aggregate(Sum('total'))['total__sum'] or 0
        context += f"El total de ventas históricas es ${total_sales}. "

    prompt = f"{context}\nUsuario: {user_message}\nAsistente:"
    
    try:
        response = model.generate_content(prompt)
        return response.text
    except Exception as e:
        return f"Lo siento, ocurrió un error al procesar tu solicitud: {str(e)}"
