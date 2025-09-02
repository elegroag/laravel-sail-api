// utils/ErrorHandler.js - Manejador de errores
export default class ErrorHandler {
	#logErrors = true;
	#showUserMessages = true;

	constructor(options = {}) {
		this.#logErrors = options.logErrors ?? true;
		this.#showUserMessages = options.showUserMessages ?? true;
	}

	handleError(error, context = 'Error desconocido') {
		if (this.#logErrors) {
			console.error(`[${context}]:`, error);
		}

		if (this.#showUserMessages) {
			this.#showErrorMessage(this.#getUserFriendlyMessage(error, context));
		}
	}

	#getUserFriendlyMessage(error, context) {
		if (error instanceof TypeError) {
			return 'Error de tipo de datos. Por favor, verifica que los datos sean válidos.';
		}

		if (error instanceof ReferenceError) {
			return 'Error de referencia. Algunos elementos de la página no se encontraron.';
		}

		if (error.message?.includes('fetch')) {
			return 'Error de conexión. Por favor, verifica tu conexión a internet.';
		}

		return `${context}. Si el problema persiste, recarga la página.`;
	}

	#showErrorMessage(message) {
		const tableBody = document.querySelector('#table-body');
		if (tableBody) {
			tableBody.innerHTML = `
          <tr>
            <td colspan="8" class="text-center text-danger">
              <i class="fas fa-exclamation-triangle me-2"></i>
              ${message}
            </td>
          </tr>
        `;
		} else {
			// Fallback a alert si no hay tabla disponible
			alert(message);
		}
	}
}
