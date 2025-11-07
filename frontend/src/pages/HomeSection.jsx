import React, { useEffect, useState } from "react";
import "bootstrap/dist/css/bootstrap.min.css";
import "../index.css";
import axios from "axios";
import { Link } from "react-router-dom";

export default function HomeSection({ usuario }) {
  const [productos, setProductos] = useState([]);

  useEffect(() => {
    axios
      .get("http://127.0.0.1:8000/api/productos")
      .then((res) => setProductos(res.data))
      .catch((err) => console.error("Error cargando productos:", err));
  }, []);

  // 🔹 Filtrar los 3 turrones clásicos
  const turronesClasicos = productos
    .filter((p) => p.categoria === "Turrones Clásicos")
    .slice(0, 3);

  return (
    <div className="card shadow-sm p-4">
      <h2 className="text-center text-brown my-3">Productos Destacados</h2>
      <p className="text-center text-muted my-3">
        ¡No te pierdas los turrones en oferta esta temporada!
      </p>

      <div className="row g-3">
        {turronesClasicos.length > 0 ? (
          turronesClasicos.map((p) => (
            <div key={p.id} className="col-md-4">
              <figure className="card">
                <Link to={`/productos/${p.id}`}><img src={p.imagenUrl} alt={p.nombre} className="card-img-top" /></Link>
                <figcaption className="card-body">
                  <h5 className="card-title">{p.nombre}</h5>
                  <p className="card-text">{p.descripcion}</p>
                  <p className="producto-categoria">
                    <strong>Categoría:</strong>{" "}
                    {p.categoria ? p.categoria : "Sin categoría"}
                  </p>
                  <h5 className="price">{p.precio} €</h5>
                  <a href="#" className="btn btn-primary">Comprar</a>
                </figcaption>
              </figure>
            </div>
          ))
        ) : (
          <p className="text-center">No hay turrones clásicos disponibles</p>
        )}
      </div>
    </div>
  );
}
