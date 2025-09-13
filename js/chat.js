// js/chat.js

// Configuración global
const API_ENDPOINT = "../";

export function initChat(chatId) {
    // Cambiamos la barra de navegación
    document.querySelector('.sidebar ul').insertAdjacentHTML(
        'beforeend',
        `<li><a href="chat_room.php?id=${chatId}" class="text-white text-decoration-none">${chatId}</a></li>`
    );

    // Obtenemos el título del chat
    fetch(`${API_ENDPOINT}chat_room.php?id=${chatId}`).then(() => {});

    // Mostramos el título de la primera vez
    loadChatTitle(chatId);

    // Cargamos el historial
    loadMessages(chatId);

    // Manejamos el envío
    const form = document.getElementById('message-form');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('msg-input');
        const text = input.value.trim();
        if (!text) return;
        await sendMessage(chatId, text);
        input.value = '';
        scrollBottom();
    });

    // Recolectar los mensajes cada 2 segundos
    setInterval(() => loadMessages(chatId), 2000);
}

// Cargar título
function loadChatTitle(id) {
    fetch(`${API_ENDPOINT}chat_room.php?id=${id}`)
        .then(r => r.text())
        .then(t => document.getElementById('chat-title').textContent = t);
}

// Envío
async function sendMessage(chatId, text) {
    const res = await fetch(`${API_ENDPOINT}messages.php?chat_id=${chatId}&msg=${encodeURIComponent(text)}`, {
        method: 'POST'
    });
    if (res.ok) {
        await loadMessages(chatId);
    } else {
        alert('Error al enviar');
    }
}

// Cargar mensajes
async function loadMessages(chatId) {
    const res = await fetch(`${API_ENDPOINT}messages.php?chat_id=${chatId}`);
    const data = await res.json();
    const container = document.getElementById('messages');
    container.innerHTML = '';
    data.messages.forEach(msg => {
        const div = document.createElement('div');
        div.className = 'my-2';
        div.innerHTML = `<strong>${msg.username}</strong> <small class="text-muted">${new Date(msg.created_at).toLocaleTimeString()}</small><p>${msg.text}</p>`;
        container.appendChild(div);
    });
    scrollBottom();
}

// Scroll a la última línea
function scrollBottom() {
    const el = document.getElementById('messages');
    el.scrollTop = el.scrollHeight;
}
