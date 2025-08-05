document.addEventListener('DOMContentLoaded', function() {
    const officerForm = document.getElementById('officerForm');
    const statusMessage = document.getElementById('statusMessage');
    const officersTable = document.getElementById('officersTable').getElementsByTagName('tbody')[0];
    
    if (!officerForm) {
        console.error('Officer form not found!');
        return;
    }

    function addOfficerToTable(officer) {
        const tbody = document.getElementById('officersTable').querySelector('tbody');
        
        // Remove the "No officers found" message if it exists
        const noOfficersRow = tbody.querySelector('tr td[colspan="5"]');
        if (noOfficersRow) {
            noOfficersRow.closest('tr').remove();
        }

        const newRow = document.createElement('tr');
        newRow.className = 'officer-row';
        
        // Set data attributes
        newRow.dataset.id = officer.id;
        newRow.dataset.lastName = officer.last_name;
        newRow.dataset.firstName = officer.first_name;
        newRow.dataset.middleName = officer.middle_name || '';
        newRow.dataset.birthdate = officer.birthdate;
        newRow.dataset.email = officer.email;
        newRow.dataset.position = officer.position;
        newRow.dataset.imagePath = officer.image_path;
        newRow.style.cursor = 'pointer';
        
        // Create the HTML for the new row
        newRow.innerHTML = `
            <td><img src="/storage/${officer.image_path}" alt="Officer Image" width="50"></td>
            <td>${officer.last_name}, ${officer.first_name} ${officer.middle_name || ''}</td>
            <td>${officer.birthdate}</td>
            <td>${officer.email}</td>
            <td>${officer.position}</td>
        `;

        // Add the new row to the beginning of the table
        tbody.insertBefore(newRow, tbody.firstChild);

        // Add a highlight effect
        newRow.style.backgroundColor = '#e6ffe6';
        setTimeout(() => {
            newRow.style.transition = 'background-color 1s ease';
            newRow.style.backgroundColor = '';
        }, 1000);
    }

    officerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submission started');

        // Create FormData object
        const formData = new FormData(officerForm);
        console.log('Form data prepared:', Object.fromEntries(formData));

        // Show loading state
        const submitButton = officerForm.querySelector('button[type="submit"]');
        submitButton.textContent = 'Adding...';
        submitButton.disabled = true;

        // Send AJAX request
        fetch('/pages/officers', { 
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            const contentType = response.headers.get('content-type');

            // Check if response is JSON before parsing
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(data => {
                    if (!response.ok) {
                        // Handle validation errors
                        if (data.errors) {
                            const errorMessages = Object.values(data.errors).flat().join('\n');
                            throw new Error(errorMessages);
                        }
                        throw new Error(data.message || `HTTP error! Status: ${response.status}`);
                    }
                    return data;
                });
            } else {
                // If response is not JSON, return text for debugging
                return response.text().then(text => {
                    console.error('Unexpected response (not JSON):', text);
                    throw new Error('Unexpected response from the server. Please try again.');
                });
            }
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.status === 'success') {
                // Add the new officer to the table
                addOfficerToTable(data.officer);
                
                // Reset the form
                officerForm.reset();
                
                // Show success message
                showStatusMessage(data.message || 'Officer Added Successfully', 'success');
            } else {
                console.error('Server returned error status:', data);
                showStatusMessage(data.message || 'Failed to add officer. Please try again.', 'danger');
            }
        })
        .catch(error => {
            console.error('Error during form submission:', error);
            showStatusMessage(error.message || 'Failed to add officer. Please try again.', 'danger');
        })
        .finally(() => {
            submitButton.textContent = 'Add Officer';
            submitButton.disabled = false;
        });
    });

    // Add click event for table rows
    const rows = document.querySelectorAll('.officer-row');
    console.log('Found officer rows:', rows.length);
    
    rows.forEach(row => {
        row.addEventListener('click', function() {
            console.log('Row clicked');
            try {
                // Get data from row attributes
                const id = this.dataset.id;
                const lastName = this.dataset.lastName;
                const firstName = this.dataset.firstName;
                const middleName = this.dataset.middleName || '';
                const birthdate = this.dataset.birthdate;
                const email = this.dataset.email;
                const position = this.dataset.position;
                const imagePath = this.dataset.imagePath;

                // Populate modal fields
                document.getElementById('update_officer_id').value = id;
                document.getElementById('update_last_name').value = lastName;
                document.getElementById('update_first_name').value = firstName;
                document.getElementById('update_middle_name').value = middleName;
                document.getElementById('update_birthdate').value = birthdate;
                document.getElementById('update_email').value = email;
                document.getElementById('update_position').value = position;
                document.getElementById('current_image').src = `/storage/${imagePath}`;

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('updateOfficerModal'));
                modal.show();
                console.log('Modal shown successfully');
            } catch (error) {
                console.error('Error showing modal:', error);
                showStatusMessage('Error loading officer data. Please try again.', 'danger');
            }
        });
    });

    // Handle update form submission
    document.getElementById('updateOfficerForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const officerId = document.getElementById('update_officer_id').value;
            const submitButton = this.querySelector('button[type="submit"]');
            
            // Show loading state
            submitButton.textContent = 'Updating...';
            submitButton.disabled = true;

            // Create FormData object
            const formData = new FormData();
            
            // Add method spoofing and CSRF token
            formData.append('_method', 'PUT');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            // Add form fields
            formData.append('last_name', document.getElementById('update_last_name').value.trim());
            formData.append('first_name', document.getElementById('update_first_name').value.trim());
            formData.append('middle_name', document.getElementById('update_middle_name').value.trim());
            formData.append('birthdate', document.getElementById('update_birthdate').value);
            formData.append('email', document.getElementById('update_email').value.trim());
            formData.append('position', document.getElementById('update_position').value);

            // Add image if selected
            const imageFile = document.getElementById('update_image').files[0];
            if (imageFile) {
                formData.append('image', imageFile);
            }

            // Send request
            const response = await fetch(`/pages/officers/${officerId}`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.status === 'success') {
                // Show success message
                showStatusMessage('Officer updated successfully!', 'success');
                
                // Close the modal
                const updateModal = bootstrap.Modal.getInstance(document.getElementById('updateOfficerModal'));
                updateModal.hide();
                
                // Reload page to show updated data
                window.location.reload();
            } else {
                // Show error message
                const errorMessage = result.errors ? Object.values(result.errors).flat().join('\n') : result.message;
                throw new Error(errorMessage || 'Failed to update officer');
            }
        } catch (error) {
            console.error('Update error:', error);
            showStatusMessage(error.message || 'Failed to update officer. Please try again.', 'danger');
        } finally {
            // Reset button state
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.textContent = 'Update Officer';
            submitButton.disabled = false;
        }
    });

    // Helper function to show status messages
    function showStatusMessage(message, type) {
        statusMessage.textContent = message;
        statusMessage.className = `alert alert-${type}`;
        statusMessage.style.display = 'block';
        
        // Hide message after 3 seconds
        setTimeout(() => {
            statusMessage.style.display = 'none';
        }, 3000);
    }
});