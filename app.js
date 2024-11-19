document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(event) {
        const variaveis = document.getElementById('variaveis').value;
        const restricoes = document.getElementById('restricoes').value;

        if (variaveis < 1 || restricoes < 1) {
            alert("Por favor, insira números positivos maiores que zero.");
            event.preventDefault();
        }
    });
});
