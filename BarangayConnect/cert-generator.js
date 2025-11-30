/**
 * Certificate Generator - Frontend JavaScript
 * Handles form interactions, autocomplete, and AJAX submissions
 */

class CertificateGenerator {
    constructor() {
        this.selectedResidentID = null;
        this.selectedBarangayID = null;
        this.autocompleteTimeout = null;
        
        // IMPORTANT: Set this to match your folder structure
        // Since files are accessed from /BarangayConnect/ already, use empty string
        this.basePath = '';
        
        this.init();
    }
    
    init() {
        this.cacheElements();
        this.loadBarangays();
        this.loadCertTypes();
        this.attachEventListeners();
        this.updateDateTime();
        setInterval(() => this.updateDateTime(), 1000);
    }
    
    cacheElements() {
        this.barangaySelect = document.getElementById('barangay');
        this.barangayLogo = document.getElementById('barangay-logo');
        this.barangayNameDisplay = document.getElementById('barangay-name-display');
        this.certTypeSelect = document.getElementById('cert-type');
        this.purposeTextarea = document.getElementById('purpose');
        this.purposeSuggestions = document.getElementById('purpose-suggestions');
        this.nameInput = document.getElementById('resident-name');
        this.autocompleteResults = document.getElementById('autocomplete-results');
        this.residentDetails = document.getElementById('resident-details');
        this.submitBtn = document.getElementById('submit-btn');
        this.form = document.getElementById('cert-form');
        this.dateTimeDisplay = document.getElementById('current-datetime');
    }
    
    async loadBarangays() {
        try {
            const response = await fetch(this.basePath + 'api/get_barangays.php');
            const result = await response.json();
            
            if (result.success) {
                this.barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
                result.data.forEach(barangay => {
                    const option = document.createElement('option');
                    option.value = barangay.barangayID;
                    option.textContent = barangay.barangayName;
                    this.barangaySelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading barangays:', error);
            this.showError('Failed to load barangays.');
        }
    }
    
    async loadCertTypes() {
        try {
            const response = await fetch(this.basePath + 'api/get_cert_types.php');
            const result = await response.json();
            
            if (result.success) {
                this.certTypeSelect.innerHTML = '<option value="">-- Select Certificate Type --</option>';
                result.data.forEach(certType => {
                    const option = document.createElement('option');
                    option.value = certType.cert_typeID;
                    option.textContent = certType.cert_types;
                    this.certTypeSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading certificate types:', error);
            this.showError('Failed to load certificate types.');
        }
    }
    
    attachEventListeners() {
        this.barangaySelect.addEventListener('change', () => this.onBarangayChange());
        this.certTypeSelect.addEventListener('change', () => this.onCertTypeChange());
        this.nameInput.addEventListener('input', () => this.onNameInput());
        this.nameInput.addEventListener('focus', () => this.onNameInput());
        this.form.addEventListener('submit', (e) => this.onSubmit(e));
        
        // Click outside to close autocomplete
        document.addEventListener('click', (e) => {
            if (!this.nameInput.contains(e.target) && !this.autocompleteResults.contains(e.target)) {
                this.hideAutocomplete();
            }
        });
    }
    
    onBarangayChange() {
        const barangayID = this.barangaySelect.value;
        this.selectedBarangayID = barangayID ? parseInt(barangayID) : null;
        
        if (barangayID) {
            const barangayName = this.barangaySelect.options[this.barangaySelect.selectedIndex].text;
            this.barangayNameDisplay.textContent = barangayName;
            
            // Update logo - using img folder in BarangayConnect
            const logoPath = `img/${barangayID}.png`;
            this.barangayLogo.src = logoPath;
            this.barangayLogo.onerror = () => {
                // Fallback to Nagcarlan logo if barangay-specific logo not found
                this.barangayLogo.src = 'img/Nagcarlan_Laguna_seal_logo.png';
            };
            
            // Reset resident selection when barangay changes
            this.selectedResidentID = null;
            this.nameInput.value = '';
            this.hideResidentDetails();
        } else {
            this.barangayNameDisplay.textContent = '';
            this.barangayLogo.src = 'img/Nagcarlan_Laguna_seal_logo.png';
        }
    }
    
    async onCertTypeChange() {
        const certTypeID = this.certTypeSelect.value;
        
        if (certTypeID) {
            // Load common purposes for this certificate type
            try {
                const response = await fetch(this.basePath + `api/get_common_purposes.php?cert_typeID=${certTypeID}`);
                const result = await response.json();
                
                if (result.success && result.data.length > 0) {
                    this.purposeSuggestions.innerHTML = '<small>Common purposes:</small> ';
                    result.data.forEach((item, index) => {
                        const badge = document.createElement('span');
                        badge.className = 'purpose-badge';
                        badge.textContent = item.purpose;
                        badge.onclick = () => {
                            this.purposeTextarea.value = item.purpose;
                        };
                        this.purposeSuggestions.appendChild(badge);
                    });
                } else {
                    this.purposeSuggestions.innerHTML = '';
                }
            } catch (error) {
                console.error('Error loading common purposes:', error);
            }
        } else {
            this.purposeSuggestions.innerHTML = '';
        }
    }
    
    onNameInput() {
        const query = this.nameInput.value.trim();
        
        console.log('Typing in name field:', query); // DEBUG
        
        clearTimeout(this.autocompleteTimeout);
        
        if (query.length < 2) {
            this.hideAutocomplete();
            return;
        }
        
        console.log('Searching for:', query); // DEBUG
        
        this.autocompleteTimeout = setTimeout(() => {
            this.searchResidents(query);
        }, 300);
    }
    
    async searchResidents(query) {
        try {
            const barangayParam = this.selectedBarangayID ? `&barangayID=${this.selectedBarangayID}` : '';
            const url = this.basePath + `api/search_residents.php?q=${encodeURIComponent(query)}${barangayParam}`;
            
            console.log('Fetching from:', url); // DEBUG
            
            const response = await fetch(url);
            const result = await response.json();
            
            console.log('Search result:', result); // DEBUG
            
            if (result.success) {
                this.displayAutocomplete(result.data);
            }
        } catch (error) {
            console.error('Error searching residents:', error);
        }
    }
    
    displayAutocomplete(residents) {
        console.log('Displaying autocomplete with', residents.length, 'residents'); // DEBUG
        
        if (residents.length === 0) {
            this.hideAutocomplete();
            return;
        }
        
        this.autocompleteResults.innerHTML = '';
        
        residents.forEach(resident => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item';
            
            const birthDate = new Date(resident.birthdate);
            const age = this.calculateAge(birthDate);
            
            item.innerHTML = `
                <strong>${this.escapeHtml(resident.first_name)} ${this.escapeHtml(resident.last_name)}</strong><br>
                <small>Age: ${age} | ${this.escapeHtml(resident.barangayName)} | ${this.escapeHtml(resident.address || 'No address')}</small>
            `;
            
            item.onclick = () => this.selectResident(resident);
            
            this.autocompleteResults.appendChild(item);
        });
        
        this.autocompleteResults.style.display = 'block';
        console.log('Autocomplete shown'); // DEBUG
    }
    
    hideAutocomplete() {
        this.autocompleteResults.style.display = 'none';
        this.autocompleteResults.innerHTML = '';
    }
    
    selectResident(resident) {
        this.selectedResidentID = resident.residentID;
        this.nameInput.value = `${resident.first_name} ${resident.last_name}`;
        this.hideAutocomplete();
        this.showResidentDetails(resident);
    }
    
    showResidentDetails(resident) {
        const birthDate = new Date(resident.birthdate);
        const age = this.calculateAge(birthDate);
        
        this.residentDetails.innerHTML = `
            <div class="resident-info">
                <strong>Selected Resident:</strong><br>
                <strong>Name:</strong> ${this.escapeHtml(resident.first_name)} ${this.escapeHtml(resident.last_name)}<br>
                <strong>Age:</strong> ${age} years old<br>
                <strong>Address:</strong> ${this.escapeHtml(resident.address || 'N/A')}, ${this.escapeHtml(resident.barangayName)}<br>
                <strong>Birthdate:</strong> ${this.formatDate(birthDate)}
            </div>
        `;
        this.residentDetails.style.display = 'block';
    }
    
    hideResidentDetails() {
        this.residentDetails.style.display = 'none';
        this.residentDetails.innerHTML = '';
    }
    
    async onSubmit(e) {
        e.preventDefault();
        
        // Validate form
        if (!this.selectedBarangayID) {
            this.showError('Please select a barangay.');
            return;
        }
        
        if (!this.selectedResidentID) {
            this.showError('Please select a resident from the autocomplete suggestions.');
            return;
        }
        
        if (!this.certTypeSelect.value) {
            this.showError('Please select a certificate type.');
            return;
        }
        
        if (!this.purposeTextarea.value.trim()) {
            this.showError('Please enter the purpose of the certificate.');
            return;
        }
        
        // Disable submit button and show loading
        this.submitBtn.disabled = true;
        this.submitBtn.innerHTML = '<span class="spinner"></span> Generating Certificate...';
        
        try {
            const response = await fetch(this.basePath + 'api/submit_request.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    residentID: this.selectedResidentID,
                    cert_typeID: parseInt(this.certTypeSelect.value),
                    purpose: this.purposeTextarea.value.trim(),
                    barangayID: this.selectedBarangayID
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showSuccess(result.message);
                
                // Open certificate in new tab
                window.open(result.data.certificateURL, '_blank');
                
                // Reset form
                this.resetForm();
            } else {
                this.showError(result.message);
            }
        } catch (error) {
            console.error('Error submitting request:', error);
            this.showError('Failed to submit certificate request. Please try again.');
        } finally {
            this.submitBtn.disabled = false;
            this.submitBtn.innerHTML = '📄 Generate Certificate';
        }
    }
    
    resetForm() {
        this.form.reset();
        this.selectedResidentID = null;
        this.selectedBarangayID = null;
        this.hideResidentDetails();
        this.hideAutocomplete();
        this.purposeSuggestions.innerHTML = '';
        this.barangayNameDisplay.textContent = '';
        this.barangayLogo.src = 'img/Nagcarlan_Laguna_seal_logo.png';
    }
    
    updateDateTime() {
        const now = new Date();
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
        };
        this.dateTimeDisplay.textContent = now.toLocaleDateString('en-US', options);
    }
    
    calculateAge(birthDate) {
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        return age;
    }
    
    formatDate(date) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    showError(message) {
        alert('❌ Error: ' + message);
    }
    
    showSuccess(message) {
        alert('✅ Success: ' + message);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new CertificateGenerator();
});