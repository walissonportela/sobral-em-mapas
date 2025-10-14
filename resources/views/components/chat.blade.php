<style>
    /* Estilos gerais para o visual do chat */
#show-chat-button {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #007bff;
    color: white;
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    padding: 10px 20px;
    border-radius: 50px;
    cursor: pointer;
    z-index: 1001;
    margin-left: 20px !important;
}

#show-chat-button:hover {
    background-color: #007bb5;
}

#show-chat-button:active {
    background-color: #007bb5;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

#chat-container {
    width: 300px;
    height: 400px;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    display: none;
    flex-direction: column;
    background-color: #ccc;
    position: fixed;
    margin-left: 20px;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
}

.chat-header {
    background-color: #007bff;
    color: #fff;
    padding: 10px;
    text-align: center;
    border-radius: 8px 8px 0px 0px;
    font-size: 16px;
    font-weight: bold;
    flex-shrink: 0;
    position: relative;
    height: 50px;
}

.chat-title {
    font-size: 18px;
    padding-top: 2px;
    text-align: center;
    font-weight: bold;
}

.fa-times {
    font-size: 20px;
    margin-right: 4px;
}

.fa-comment {
    padding-top: 3px;
    margin-right: 4px;
}

#messages {
    flex: 1;
    padding: 10px;
    overflow-y: auto;
    background-color: #f7f7f7;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.message.received {
    align-self: flex-start;
    background-color: #e0e0e0;
    color: #000;
    padding: 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
    max-width: 80%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.message.sent {
    align-self: flex-end;
    background-color: #007bff;
    color: #fff;
    padding: 10px;
    border-radius: 10px;
    max-width: 80%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

#message-input-container {
    display: flex;
    padding: 10px;
    background-color: #007bff;
    flex-shrink: 0;
    border-radius: 0px 0px 8px 8px;
}

#message-input {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 4px;
    outline: none;
}

#send-button {
    background-color: #0056b3;
    color: white;
    border: none;
    padding: 10px;
    margin-left: 10px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

#send-button:hover {
    background-color: #003d99;
}

#toggle-chat-button {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    color: #fff;
    cursor: pointer;
    font-size: 20px;
}

.welcome-message {
    background-color: #007bff;
    color: #fff;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 10px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: -10px;
}

.welcome-message p {
    margin: 0;
    font-size: 14px;
}

.welcome-message p:first-child {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 5px;
}

/* Estilos para o chat em telas menores */
@media (max-width: 768px) {
    #show-chat-button {
        padding: 10px;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        text-align: center;
        font-size: 20px;
        background-color: #007bff;
        color: white;
        right: 10px;
        bottom: 10px;
    }

    #show-chat-button i {
        margin-right: 0;
    }

    #show-chat-button:hover {
        background-color: #007bb5;
    }
    
    #show-chat-button:active {
        background-color: #007bb5;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }
    #show-chat-button span {
        display: none;
    }
    
    #chat-container {
        width: 100vw;            
        height: 70vh;          
        border-radius: 0;         
        margin-left: 0;
        box-shadow: none;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        transform: translateY(100%); 
        transition: transform 0.6s ease;
        display: flex;
        flex-direction: column;
        z-index: 1050;
    }

    #chat-container.open {
        transform: translateY(0);
    }
    
    .chat-header {
        border-radius: 0;
        height: 50px;   
        font-size: 16px;
        flex-shrink: 0;
    }

    #messages {
        flex: 1; 
        overflow-y: auto;
        background-color: #f7f7f7; 
    }

    #message-input-container {
        border-radius: 0;
        flex-shrink: 0;
    }
}

/* Botão que abre o chat */
#show-chat-button img {
    height: 2rem; /* tamanho relativo à fonte */
    position: relative;
    top: -0.2rem; /* sobe levemente */
}

/* Logo no cabeçalho do chat */
.chat-header .chat-title img {
    height: 3rem; /* relativo ao tamanho de texto base */
    position: relative;
    top: -0.5rem;
}

/* Ajustes para telas menores (até 480px) */
@media (max-width: 480px) {
    #show-chat-button img {
        height: 1.6rem; /* menor no celular */
        top: -0.15rem;
    }

    .chat-header .chat-title img {
        height: 2.2rem; /* menor no celular */
        top: -0.4rem;
    }
}
</style>

<button id="show-chat-button">
    <img src="img/logo_t.png" alt="Logo NaVISOL">
    <span> Chat - NaVISOL</span>
</button>

<div id="chat-container">
    <div class="chat-header">
        <div class="chat-title">
            <img src="img/logo_t.png" alt="Logo NaVISOL">
            Chat - NaVISOL
        </div>
        <button id="toggle-chat-button">
            <i class="fas fa-times" id="toggle-icon"></i>
        </button>
    </div>

    <div id="messages">
        <div class="welcome-message">
            <p><strong>Bem-vindo ao NaVISOL!</strong></p>

             <p class="chat-hint">Tente perguntar sobre um destino, um ponto turístico ou simplesmente diga "olá".</p>
        </div>
        <hr>
        <div class="message received"></div>
    </div>

    <div id="message-input-container">
        <input type="text" id="message-input" placeholder="Digite sua mensagem...">
        <button id="send-button">Enviar</button>
    </div>
</div>
