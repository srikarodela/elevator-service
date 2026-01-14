// Elevator Services App - LocalStorage Data Management

// Initialize LocalStorage with default data if empty
function initializeLocalStorage() {
    if (!localStorage.getItem('serviceRequests')) {
        const defaultRequests = [];
        localStorage.setItem('serviceRequests', JSON.stringify(defaultRequests));
    }
    
    if (!localStorage.getItem('modules')) {
        const defaultModules = [
            {
                id: 1,
                title: 'Passenger Lift',
                description: 'Comfortable and safe passenger elevators for commercial and residential buildings.',
                capacity: '8-20 persons',
                speed: '1.0-2.5 m/s',
                features: ['Smooth operation', 'Emergency brake system', 'Automatic doors', 'Energy efficient']
            },
            {
                id: 2,
                title: 'Goods Lift',
                description: 'Heavy-duty elevators designed for transporting goods and materials.',
                capacity: '500-3000 kg',
                speed: '0.5-1.5 m/s',
                features: ['Robust construction', 'High load capacity', 'Durable finish', 'Safety sensors']
            },
            {
                id: 3,
                title: 'Hospital Lift',
                description: 'Specialized elevators for hospitals with stretcher accommodation.',
                capacity: '13-26 persons',
                speed: '1.0-2.0 m/s',
                features: ['Stretcher accommodation', 'Hygienic design', 'Smooth ride', 'Emergency power']
            },
            {
                id: 4,
                title: 'Home Lift',
                description: 'Compact and elegant elevators designed for residential use.',
                capacity: '3-5 persons',
                speed: '0.3-0.6 m/s',
                features: ['Space saving', 'Quiet operation', 'Modern design', 'Easy maintenance']
            }
        ];
        localStorage.setItem('modules', JSON.stringify(defaultModules));
    }
    
    if (!localStorage.getItem('adminCredentials')) {
        const defaultAdmin = {
            username: 'admin',
            password: 'admin123'
        };
        localStorage.setItem('adminCredentials', JSON.stringify(defaultAdmin));
    }
}

// Service Request Management
class ServiceRequestManager {
    static addRequest(requestData) {
        const requests = this.getAllRequests();
        const newRequest = {
            id: Date.now(),
            ...requestData,
            status: 'pending',
            createdAt: new Date().toISOString()
        };
        requests.push(newRequest);
        localStorage.setItem('serviceRequests', JSON.stringify(requests));
        return newRequest;
    }
    
    static getAllRequests() {
        const requests = localStorage.getItem('serviceRequests');
        return requests ? JSON.parse(requests) : [];
    }
    
    static updateRequestStatus(requestId, newStatus) {
        const requests = this.getAllRequests();
        const requestIndex = requests.findIndex(req => req.id == requestId);
        if (requestIndex !== -1) {
            requests[requestIndex].status = newStatus;
            localStorage.setItem('serviceRequests', JSON.stringify(requests));
            return true;
        }
        return false;
    }
    
    static deleteRequest(requestId) {
        const requests = this.getAllRequests();
        const filteredRequests = requests.filter(req => req.id != requestId);
        localStorage.setItem('serviceRequests', JSON.stringify(filteredRequests));
        return true;
    }
}

// Module Management
class ModuleManager {
    static getAllModules() {
        const modules = localStorage.getItem('modules');
        return modules ? JSON.parse(modules) : [];
    }
    
    static addModule(moduleData) {
        const modules = this.getAllModules();
        const newModule = {
            id: Date.now(),
            ...moduleData
        };
        modules.push(newModule);
        localStorage.setItem('modules', JSON.stringify(modules));
        return newModule;
    }
    
    static updateModule(moduleId, moduleData) {
        const modules = this.getAllModules();
        const moduleIndex = modules.findIndex(mod => mod.id == moduleId);
        if (moduleIndex !== -1) {
            modules[moduleIndex] = { ...modules[moduleIndex], ...moduleData };
            localStorage.setItem('modules', JSON.stringify(modules));
            return true;
        }
        return false;
    }
    
    static deleteModule(moduleId) {
        const modules = this.getAllModules();
        const filteredModules = modules.filter(mod => mod.id != moduleId);
        localStorage.setItem('modules', JSON.stringify(filteredModules));
        return true;
    }
}

// Admin Authentication
class AdminAuth {
    static login(username, password) {
        const credentials = JSON.parse(localStorage.getItem('adminCredentials') || '{}');
        if (username === credentials.username && password === credentials.password) {
            localStorage.setItem('adminLoggedIn', 'true');
            return true;
        }
        return false;
    }
    
    static logout() {
        localStorage.removeItem('adminLoggedIn');
    }
    
    static isLoggedIn() {
        return localStorage.getItem('adminLoggedIn') === 'true';
    }
    
    static requireAuth() {
        if (!this.isLoggedIn()) {
            window.location.href = 'admin.html';
            return false;
        }
        return true;
    }
}

// Form Validation
function validateServiceForm(formData) {
    const errors = [];
    
    if (!formData.name || formData.name.trim() === '') {
        errors.push('Name is required');
    }
    
    if (!formData.phone || formData.phone.trim() === '') {
        errors.push('Phone is required');
    } else if (!/^\d{10,15}$/.test(formData.phone.replace(/\D/g, ''))) {
        errors.push('Please enter a valid phone number');
    }
    
    if (!formData.email || formData.email.trim() === '') {
        errors.push('Email is required');
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
        errors.push('Please enter a valid email address');
    }
    
    if (!formData.location || formData.location.trim() === '') {
        errors.push('Location is required');
    }
    
    if (!formData.elevatorType || formData.elevatorType === '') {
        errors.push('Elevator type is required');
    }
    
    if (!formData.problem || formData.problem.trim() === '') {
        errors.push('Problem description is required');
    }
    
    return errors;
}

// Show message function
function showMessage(message, type = 'success') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `${type}-message`;
    messageDiv.textContent = message;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(messageDiv, container.firstChild);
        
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
}

// Format date for display
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
}

// Initialize the app when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeLocalStorage();
});

// Export classes for use in other scripts
window.ServiceRequestManager = ServiceRequestManager;
window.ModuleManager = ModuleManager;
window.AdminAuth = AdminAuth;
window.validateServiceForm = validateServiceForm;
window.showMessage = showMessage;
window.formatDate = formatDate;
