/**
 * Sistema de Validación en Cliente - ID Cultural
 * Valida: email, teléfono, archivos, longitud mínima, etc.
 */

class FormValidator {
  constructor(formSelector) {
    this.form = document.querySelector(formSelector);
    this.errors = {};
    this.rules = {};
    this.init();
  }

  init() {
    if (!this.form) return;
    this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    this.attachFieldListeners();
  }

  attachFieldListeners() {
    const fields = this.form.querySelectorAll('[data-validate]');
    fields.forEach(field => {
      field.addEventListener('blur', () => this.validateField(field));
      field.addEventListener('input', () => this.clearFieldError(field));
    });
  }

  // Validaciones individuales
  static rules = {
    // Email validation
    email: (value) => {
      const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return regex.test(value) ? true : 'Email inválido';
    },

    // Teléfono (formato: +XX XXXXXXXXXX)
    phone: (value) => {
      const regex = /^(\+\d{1,3})?\s?(\d{7,15})$/;
      return regex.test(value.replace(/[\s\-]/g, '')) ? true : 'Teléfono inválido';
    },

    // Contraseña (mín 8 caracteres, 1 mayúscula, 1 número)
    password: (value) => {
      if (value.length < 8) return 'Mínimo 8 caracteres';
      if (!/[A-Z]/.test(value)) return 'Debe contener una mayúscula';
      if (!/[0-9]/.test(value)) return 'Debe contener un número';
      return true;
    },

    // Campo requerido
    required: (value) => {
      return value.trim() !== '' ? true : 'Campo requerido';
    },

    // Longitud mínima
    minLength: (value, min) => {
      return value.length >= min ? true : `Mínimo ${min} caracteres`;
    },

    // Longitud máxima
    maxLength: (value, max) => {
      return value.length <= max ? true : `Máximo ${max} caracteres`;
    },

    // Solo números
    number: (value) => {
      return /^\d+$/.test(value) ? true : 'Solo números permitidos';
    },

    // Solo letras y espacios
    alpha: (value) => {
      return /^[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+$/.test(value) ? true : 'Solo letras permitidas';
    },

    // URL válida
    url: (value) => {
      try {
        new URL(value);
        return true;
      } catch {
        return 'URL inválida';
      }
    },

    // Archivo - validar tipo
    fileType: (file, allowedTypes) => {
      if (!file) return true;
      const types = allowedTypes.split(',').map(t => t.trim());
      return types.includes(file.type) ? true : `Tipo de archivo no permitido. Permitidos: ${allowedTypes}`;
    },

    // Archivo - validar tamaño (en MB)
    fileSize: (file, maxMB) => {
      if (!file) return true;
      const maxBytes = maxMB * 1024 * 1024;
      return file.size <= maxBytes ? true : `Tamaño máximo: ${maxMB}MB`;
    },

    // Comparar dos campos (ej: contraseña repetida)
    match: (value, otherFieldSelector) => {
      const otherField = document.querySelector(otherFieldSelector);
      if (!otherField) return true;
      return value === otherField.value ? true : 'Los campos no coinciden';
    },

    // Validar edad (documentos)
    minAge: (value, minAge) => {
      const birthDate = new Date(value);
      const today = new Date();
      let age = today.getFullYear() - birthDate.getFullYear();
      const monthDiff = today.getMonth() - birthDate.getMonth();
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }
      return age >= minAge ? true : `Debe ser mayor de ${minAge} años`;
    },

    // Validar fecha futura
    futureDate: (value) => {
      const date = new Date(value);
      const today = new Date();
      return date > today ? true : 'La fecha debe ser en el futuro';
    },

    // Validar JSON
    json: (value) => {
      try {
        JSON.parse(value);
        return true;
      } catch {
        return 'JSON inválido';
      }
    }
  };

  validateField(field) {
    const validations = field.getAttribute('data-validate').split('|');
    let error = null;

    for (const validation of validations) {
      const [ruleName, ...params] = validation.split(':');
      const rule = FormValidator.rules[ruleName];

      if (!rule) continue;

      const value = field.type === 'file' ? field.files[0] : field.value;
      const result = rule(value, ...params);

      if (result !== true) {
        error = result;
        break;
      }
    }

    if (error) {
      this.setFieldError(field, error);
      return false;
    } else {
      this.clearFieldError(field);
      return true;
    }
  }

  setFieldError(field, message) {
    field.classList.add('is-invalid');
    field.classList.remove('is-valid');

    let errorElement = field.parentElement.querySelector('.invalid-feedback');
    if (!errorElement) {
      errorElement = document.createElement('div');
      errorElement.className = 'invalid-feedback d-block mt-1';
      field.parentElement.appendChild(errorElement);
    }
    errorElement.textContent = message;
    this.errors[field.name] = message;
  }

  clearFieldError(field) {
    field.classList.remove('is-invalid');
    field.classList.add('is-valid');

    const errorElement = field.parentElement.querySelector('.invalid-feedback');
    if (errorElement) {
      errorElement.remove();
    }
    delete this.errors[field.name];
  }

  handleSubmit(e) {
    const fields = this.form.querySelectorAll('[data-validate]');
    let isValid = true;

    fields.forEach(field => {
      if (!this.validateField(field)) {
        isValid = false;
      }
    });

    if (!isValid) {
      e.preventDefault();
      this.showErrorMessage('Por favor corrige los errores del formulario');
      return false;
    }

    return true;
  }

  showErrorMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible fade show';
    alertDiv.innerHTML = `
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    this.form.insertBefore(alertDiv, this.form.firstChild);
  }

  isValid() {
    return Object.keys(this.errors).length === 0;
  }

  getErrors() {
    return this.errors;
  }

  reset() {
    this.form.reset();
    this.errors = {};
    this.form.querySelectorAll('.is-invalid, .is-valid').forEach(field => {
      field.classList.remove('is-invalid', 'is-valid');
    });
  }
}

// Exportar para uso global
window.FormValidator = FormValidator;

// Auto-inicializar en DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
  // Buscar todos los formularios con clase 'needs-validation'
  document.querySelectorAll('form[data-validate="true"]').forEach(form => {
    new FormValidator(`#${form.id}`);
  });
});

/**
 * Ejemplos de uso:
 * 
 * <form id="registroForm" data-validate="true">
 *   <input type="email" name="email" data-validate="email|required" class="form-control">
 *   <input type="tel" name="phone" data-validate="phone" class="form-control">
 *   <input type="password" name="password" data-validate="password|required" class="form-control">
 *   <input type="password" name="password_confirm" data-validate="match:#password|required" class="form-control">
 *   <input type="text" name="nombre" data-validate="required|minLength:3|maxLength:50|alpha" class="form-control">
 *   <input type="file" name="foto" data-validate="fileType:image/jpeg,image/png|fileSize:5" class="form-control">
 *   <input type="date" name="fecha_nacimiento" data-validate="minAge:18" class="form-control">
 *   <button type="submit" class="btn btn-primary">Enviar</button>
 * </form>
 */
