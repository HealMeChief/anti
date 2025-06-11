// Массив для хранения товаров в корзине (теперь синхронизируется с бэкендом)
let cart = [];

// Инициализация корзины при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    loadCart();
    setupEventListeners();
});

// Настройка всех обработчиков событий
function setupEventListeners() {
    // Обработчики для меню
    const menuButton = document.querySelector('.image-button');
    const navLinks = document.querySelector('.nav-links');
    const menuImage = menuButton?.querySelector('img');

    if (menuButton && navLinks && menuImage) {
        menuButton.addEventListener('click', (event) => {
            event.stopPropagation();
            navLinks.classList.toggle('open');
            menuImage.src = navLinks.classList.contains('open') ? 'close-button.png' : 'menu.png';
        });

        document.addEventListener('click', (event) => {
            if (!navLinks.contains(event.target)) {
                navLinks.classList.remove('open');
                menuImage.src = 'menu.png';
            }
        });

        navLinks.addEventListener('click', (event) => {
            event.stopPropagation();
        });
    }

    // Обработчики для кнопок "В корзину"
    document.querySelectorAll('.buy-button button').forEach(button => {
        button.addEventListener('click', (e) => {
            const itemElement = e.target.closest('.item');
            if (itemElement) {
                addToCart(itemElement);
            }
        });
    });
}

// Улучшенная функция загрузки корзины
async function loadCart() {
    const cartContainer = document.getElementById('cart-container');
    if (!cartContainer) return;

    try {
        const response = await fetch('/includes/cart-api.php?action=get');
        const { items } = await response.json();
        
        if (items && items.length > 0) {
            cartContainer.innerHTML = items.map(item => `
                <div class="k-item">
                    <div class="k-img">
                        <img src="${item.image_url}" alt="${item.name}">
                    </div>
                    <div class="k-text">
                        <p>${item.name}</p>
                        <p>${item.price} ₽</p>
                    </div>
                    <div class="k-controls">
                        <button class="k-minus" onclick="changeQuantity(${item.product_id}, -1)">-</button>
                        <span class="k-quantity">${item.quantity}</span>
                        <button class="k-plus" onclick="changeQuantity(${item.product_id}, 1)">+</button>
                        <button class="k-remove" onclick="removeFromCart(${item.product_id})">Удалить</button>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('order-button').disabled = false;
        } else {
            cartContainer.innerHTML = `
                <div id="empty-message" style="text-align: center; font-size: 18px; color: grey; margin-top: 20px; height: 40vh;">
                    Ваша корзина пуста. Добавьте товары, чтобы начать покупку!
                </div>`;
            document.getElementById('order-button').disabled = true;
        }
    } catch (error) {
        console.error('Ошибка загрузки корзины:', error);
        cartContainer.innerHTML = `
            <div style="color: red;">
                Ошибка загрузки корзины. Пожалуйста, обновите страницу.
            </div>`;
    }
}

// Сохранение корзины на сервер
async function saveCartToServer() {
    try {
        await fetch('/includes/cart-api.php?action=update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ items: cart })
        });
    } catch (error) {
        console.error('Ошибка сохранения корзины:', error);
        // Fallback: сохраняем в localStorage
        localStorage.setItem('cart', JSON.stringify(cart));
    }
}

// Добавление товара в корзину
// Упрощенная функция добавления в корзину
async function addToCart(itemElement) {
    const productId = itemElement.dataset.id;
    
    try {
        const response = await fetch(`/includes/cart-api.php?action=add&product_id=${productId}`);
        const result = await response.json();
        
        if (result.success) {
            showNotification('Товар добавлен в корзину!');
            // Обновляем отображение корзины, если находимся на её странице
            if (window.location.pathname.includes('corzina.php')) {
                loadCart();
            }
        } else {
            showNotification('Ошибка: ' + (result.error || 'Не удалось добавить товар'), true);
        }
    } catch (error) {
        console.error('Ошибка:', error);
        showNotification('Ошибка соединения с сервером', true);
    }
}

// Удаление товара из корзины
async function removeFromCart(itemId) {
    try {
        const response = await fetch(`/includes/cart-api.php?action=remove&product_id=${itemId}`);
        const result = await response.json();
        
        if (result.success) {
            cart = cart.filter(item => item.id !== itemId);
            renderCart();
        }
    } catch (error) {
        console.error('Ошибка удаления из корзины:', error);
        // Fallback: локальное удаление
        cart = cart.filter(item => item.id !== itemId);
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
    }
}

// Изменение количества товара
async function changeQuantity(itemId, delta) {
    const item = cart.find(item => item.id === itemId);
    if (!item) return;

    const newQuantity = item.quantity + delta;
    
    if (newQuantity <= 0) {
        await removeFromCart(itemId);
        return;
    }

    try {
        const response = await fetch('/includes/cart-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'update',
                product_id: itemId,
                quantity: newQuantity
            })
        });
        
        const result = await response.json();
        if (result.success) {
            item.quantity = newQuantity;
            renderCart();
        }
    } catch (error) {
        console.error('Ошибка изменения количества:', error);
        // Fallback: локальное изменение
        item.quantity = newQuantity;
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
    }
}

// Оформление заказа
async function placeOrder() {
    if (cart.length === 0) return;

    try {
        const customerName = prompt('Введите ваше имя:');
        const customerPhone = prompt('Введите ваш телефон:');
        const shippingAddress = prompt('Введите адрес доставки:');

        if (!customerName || !customerPhone || !shippingAddress) {
            alert('Все поля обязательны для заполнения!');
            return;
        }

        const response = await fetch('/includes/order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                items: cart,
                customer_name: customerName,
                customer_phone: customerPhone,
                shipping_address: shippingAddress
            })
        });

        const result = await response.json();
        
        if (result.success) {
            cart = [];
            await saveCartToServer();
            renderCart();
            showNotification('Заказ успешно оформлен! Номер заказа: ' + result.order_id);
        } else {
            alert('Ошибка при оформлении заказа: ' + (result.error || 'Неизвестная ошибка'));
        }
    } catch (error) {
        console.error('Ошибка оформления заказа:', error);
        alert('Ошибка при оформлении заказа. Пожалуйста, попробуйте позже.');
    }
}

// Рендер корзины (остаётся практически без изменений)
function renderCart() {
    const cartContainer = document.getElementById('cart-container');
    if (!cartContainer) return;

    cartContainer.innerHTML = '';

    if (cart.length === 0) {
        cartContainer.innerHTML = `
            <div id="empty-message" style="text-align: center; font-size: 18px; color: grey; margin-top: 20px; height: 40vh;">
                Ваша корзина пуста. Добавьте товары, чтобы начать покупку!
            </div>`;
    } else {
        cart.forEach(item => {
            const cartItem = document.createElement('div');
            cartItem.classList.add('k-item');

            cartItem.innerHTML = `
                <div class="k-img">
                    <img src="${item.img}" alt="${item.name}">
                </div>
                <div class="k-text">
                    <p>${item.name}</p>
                    <p>${item.price}</p>
                </div>
                <div class="k-controls">
                    <button class="k-minus" onclick="changeQuantity('${item.id}', -1)">-</button>
                    <span class="k-quantity">${item.quantity}</span>
                    <button class="k-plus" onclick="changeQuantity('${item.id}', 1)">+</button>
                    <button class="k-remove" onclick="removeFromCart('${item.id}')">Удалить</button>
                </div>
            `;

            cartContainer.appendChild(cartItem);
        });
    }

    updateOrderButtonState();
}

// Обновление состояния кнопки "Заказать"
function updateOrderButtonState() {
    const orderButton = document.getElementById('order-button');
    if (orderButton) {
        orderButton.disabled = cart.length === 0;
    }
}

// Вспомогательная функция для показа уведомлений
function showNotification(message, isError = false) {
    const notification = document.createElement('div');
    notification.style.position = 'fixed';
    notification.style.bottom = '20px';
    notification.style.right = '20px';
    notification.style.padding = '10px 20px';
    notification.style.backgroundColor = isError ? '#f44336' : '#4CAF50';
    notification.style.color = 'white';
    notification.style.borderRadius = '5px';
    notification.style.zIndex = '1000';
    notification.style.transition = 'opacity 0.5s';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 500);
    }, 3000);
}

// Инициализация при загрузке страницы корзины
if (window.location.pathname.includes('corzina.php')) {
    document.addEventListener('DOMContentLoaded', loadCart);
}

// Экспортируем функции в глобальную область видимости
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.changeQuantity = changeQuantity;
window.placeOrder = placeOrder;
window.renderCart = renderCart;



async function updateCartCounter() {
    const response = await fetch('/includes/cart.php?action=get');
    const { items } = await response.json();
    const count = items ? items.reduce((sum, item) => sum + item.quantity, 0) : 0;
    
    let counter = document.getElementById('cart-counter');
    if (!counter) {
        counter = document.createElement('span');
        counter.id = 'cart-counter';
        document.querySelector('.nav-bar-r').appendChild(counter);
    }
    counter.textContent = count > 0 ? ` (${count})` : '';
}

// Вызывать после каждого изменения корзины