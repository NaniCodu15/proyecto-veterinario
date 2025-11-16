// Configuración global de Axios para las peticiones AJAX utilizadas en el dashboard.
import axios from 'axios';
window.axios = axios;

// Cabecera requerida por Laravel para detectar peticiones XMLHttpRequest y aplicar middlewares apropiados.
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
