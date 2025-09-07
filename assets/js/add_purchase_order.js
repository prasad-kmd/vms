document.addEventListener('DOMContentLoaded', function() {
    const vendorSelect = document.getElementById('vendor-select');
    const productSelectionArea = document.getElementById('product-selection-area');
    const productSelect = document.getElementById('product-select');
    const addItemBtn = document.getElementById('add-item-btn');
    const orderItemsTable = document.getElementById('order-items-table');
    const orderItemsTbody = orderItemsTable.getElementsByTagName('tbody')[0];
    const totalAmountDisplay = document.getElementById('total-amount-display');
    const submitBtn = document.querySelector('input[type="submit"]');
    const orderForm = document.getElementById('order-form');
    let orderItems = {}; // To keep track of items in the order

    vendorSelect.addEventListener('change', function() {
        const vendorId = this.value;
        productSelect.innerHTML = '';
        orderItemsTbody.innerHTML = ''; // Clear existing items
        orderItems = {};
        updateTotal();

        if (vendorId) {
            // Fetch products for the selected vendor
            fetch(`get_products_by_vendor.php?vendor_id=${vendorId}`)
                .then(response => response.json())
                .then(data => {
                    productSelectionArea.style.display = 'block';
                    if (data.length > 0) {
                        productSelect.innerHTML = '<option value="">Select a product</option>';
                        data.forEach(product => {
                            const option = new Option(`${product.ProductName} ($${product.Price})`, product.ProductID);
                            option.dataset.price = product.Price;
                            option.dataset.name = product.ProductName;
                            productSelect.add(option);
                        });
                    } else {
                        productSelect.innerHTML = '<option value="">No products found for this vendor</option>';
                    }
                });
        } else {
            productSelectionArea.style.display = 'none';
        }
    });

    addItemBtn.addEventListener('click', function() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            alert('Please select a product.');
            return;
        }

        const productId = selectedOption.value;
        const productName = selectedOption.dataset.name;
        const productPrice = parseFloat(selectedOption.dataset.price);
        const quantity = parseInt(document.getElementById('quantity-input').value);

        if (isNaN(quantity) || quantity < 1) {
            alert('Please enter a valid quantity.');
            return;
        }

        if (orderItems[productId]) {
            // If item already exists, update its quantity
            orderItems[productId].quantity += quantity;
            const existingRow = document.getElementById(`item-row-${productId}`);
            existingRow.cells[1].textContent = orderItems[productId].quantity;
            existingRow.cells[3].textContent = '$' + (orderItems[productId].quantity * productPrice).toFixed(2);
            // Update hidden input
            existingRow.querySelector('input[name="quantity[]"]').value = orderItems[productId].quantity;
        } else {
            // Add new item
            orderItems[productId] = { name: productName, price: productPrice, quantity: quantity };

            const newRow = orderItemsTbody.insertRow();
            newRow.id = `item-row-${productId}`;
            newRow.innerHTML = `
                <td>
                    ${productName}
                    <input type="hidden" name="product_id[]" value="${productId}">
                </td>
                <td><input type="number" name="quantity[]" value="${quantity}" class="form-control" min="1"></td>
                <td>$${productPrice.toFixed(2)}</td>
                <td>$${(productPrice * quantity).toFixed(2)}</td>
                <td><button type="button" class="btn btn-danger btn-sm remove-item-btn">Remove</button></td>
            `;
        }

        orderItemsTable.style.display = 'table';
        updateTotal();
    });

    orderItemsTbody.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-item-btn')) {
            const row = e.target.closest('tr');
            const productId = row.querySelector('input[name="product_id[]"]').value;
            delete orderItems[productId];
            row.remove();
            updateTotal();
        }
    });

    orderItemsTbody.addEventListener('change', function(e) {
        if (e.target && e.target.name === 'quantity[]') {
            const row = e.target.closest('tr');
            const productId = row.querySelector('input[name="product_id[]"]').value;
            const newQuantity = parseInt(e.target.value);
            if (newQuantity > 0) {
                orderItems[productId].quantity = newQuantity;
                row.cells[3].textContent = '$' + (orderItems[productId].price * newQuantity).toFixed(2);
                updateTotal();
            } else {
                // If quantity is invalid, reset to old value
                e.target.value = orderItems[productId].quantity;
            }
        }
    });


    function updateTotal() {
        let total = 0;
        for (const id in orderItems) {
            total += orderItems[id].price * orderItems[id].quantity;
        }
        totalAmountDisplay.textContent = '$' + total.toFixed(2);

        // Enable/disable submit button
        submitBtn.disabled = total === 0;
        if (orderItemsTbody.rows.length === 0) {
            orderItemsTable.style.display = 'none';
        }
    }
});
