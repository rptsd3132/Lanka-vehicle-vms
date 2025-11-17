// script.js
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('productID');
    const productName = document.getElementById('productName');
    const priceInput = document.getElementById('price');
    const quantityInput = document.getElementById('quantity');
    const totalAmount = document.getElementById('totalAmount');

    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.dataset.price;
        const name = selectedOption.dataset.name;

        productName.value = name;
        priceInput.value = price;
        calculateTotal();
    });

    function calculateTotal() {
        const price = parseFloat(priceInput.value) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        const total = price * quantity;
        totalAmount.value = total.toFixed(2);
    }

    quantityInput.addEventListener('input', calculateTotal);
});