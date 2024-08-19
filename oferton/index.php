<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ofertas del Día</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }
    .container {
      width: 90%;
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }
    .header {
      text-align: center;
      margin-bottom: 30px;
    }
    .header h1 {
      font-size: 2.5rem;
      color: #333;
      margin: 0;
    }
    .header p {
      font-size: 1.2rem;
      color: #777;
    }
    .filters {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
    }
    .filters select {
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 1rem;
      background-color: #fff;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }
    .filters select:hover {
      border-color: #007bff;
    }
    .offers-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
    }
    .offer-card {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 6px 12px rgba(0,0,0,0.1);
      transition: box-shadow 0.3s, transform 0.3s;
    }
    .offer-card:hover {
      box-shadow: 0 12px 24px rgba(0,0,0,0.2);
      transform: translateY(-10px);
    }
    .offer-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }
    .offer-info {
      padding: 20px;
    }
    .offer-info h3 {
      font-size: 1.4rem;
      margin: 0;
      color: #333;
    }
    .offer-info .price {
      margin: 10px 0;
    }
    .offer-info .price span.old-price {
      text-decoration: line-through;
      color: #888;
      font-size: 1rem;
    }
    .offer-info .price span.new-price {
      font-weight: bold;
      color: #e53935;
      font-size: 1.2rem;
    }
    .rating {
      display: flex;
      align-items: center;
      gap: 5px;
      color: #fbc02d;
    }
    .rating svg {
      width: 20px;
      height: 20px;
    }
    /* Responsive Design */
    @media (max-width: 768px) {
      .filters {
        flex-direction: column;
        align-items: flex-start;
      }
      .filters select {
        width: 100%;
        margin-bottom: 10px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Ofertas del Día</h1>
      <p>Descubre las mejores ofertas del momento</p>
    </div>
    <div class="filters">
      <div>
        <label for="sort">Ordenar por:</label>
        <select id="sort">
          <option value="popularity">Popularidad</option>
          <option value="price">Precio</option>
          <option value="oldPrice">Precio original</option>
        </select>
      </div>
      <div>
        <label for="filter">Filtrar por:</label>
        <select id="filter">
          <option value="">Todas las categorías</option>
          <option value="Electrónica">Electrónica</option>
          <option value="Hogar">Hogar</option>
          <option value="Deportes">Deportes</option>
        </select>
      </div>
    </div>
    <div class="offers-grid" id="offers-grid">
      <!-- Las ofertas se cargarán aquí mediante JavaScript -->
    </div>
  </div>

  <script>
    const offers = [
      { id: 1, image: "https://http2.mlstatic.com/D_NQ_NP_2X_785340-MLU77445958626_072024-F.webp", title: "Auriculares inalámbricos", oldPrice: 99.99, newPrice: 79.99, category: "Electrónica", popularity: 4.8 },
      { id: 2, image: "images/camara.jpg", title: "Cámara digital 4K", oldPrice: 499.99, newPrice: 399.99, category: "Electrónica", popularity: 4.6 },
      { id: 3, image: "images/sabanas.jpg", title: "Juego de sábanas de lujo", oldPrice: 79.99, newPrice: 59.99, category: "Hogar", popularity: 4.7 },
      { id: 4, image: "images/bicicleta.jpg", title: "Bicicleta plegable", oldPrice: 299.99, newPrice: 249.99, category: "Deportes", popularity: 4.5 },
      { id: 5, image: "images/batidora.jpg", title: "Batidora de mano", oldPrice: 49.99, newPrice: 39.99, category: "Hogar", popularity: 4.8 },
      { id: 6, image: "images/smartwatch.jpg", title: "Smartwatch deportivo", oldPrice: 199.99, newPrice: 159.99, category: "Electrónica", popularity: 4.7 }
    ];

    const sortSelect = document.getElementById('sort');
    const filterSelect = document.getElementById('filter');
    const offersGrid = document.getElementById('offers-grid');

    function renderOffers(filteredOffers) {
      offersGrid.innerHTML = filteredOffers.map(offer => `
        <div class="offer-card">
          <img src="${offer.image}" alt="${offer.title}">
          <div class="offer-info">
            <h3>${offer.title}</h3>
            <div class="price">
              <span class="old-price">$${offer.oldPrice.toFixed(2)}</span>
              <span class="new-price">$${offer.newPrice.toFixed(2)}</span>
            </div>
            <div class="rating">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" /></svg>
              <span>${offer.popularity.toFixed(1)}</span>
            </div>
          </div>
        </div>
      `).join('');
    }

    function updateOffers() {
      const sortBy = sortSelect.value;
      const filterCategory = filterSelect.value;
      
      const filteredOffers = offers
        .filter(offer => filterCategory ? offer.category === filterCategory : true)
        .sort((a, b) => {
          switch (sortBy) {
            case 'price':
              return a.newPrice - b.newPrice;
            case 'oldPrice':
              return a.oldPrice - b.oldPrice;
            case 'popularity':
              return b.popularity - a.popularity;
            default:
              return 0;
          }
        });
      
      renderOffers(filteredOffers);
    }

    sortSelect.addEventListener('change', updateOffers);
    filterSelect.addEventListener('change', updateOffers);

    // Initial render
    updateOffers();
  </script>
</body>
</html>
