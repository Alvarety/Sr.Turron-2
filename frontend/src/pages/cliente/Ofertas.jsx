import React, { useEffect, useState } from "react";
import axios from "axios";
import { Link } from "react-router-dom";

export default function Ofertas() {
  const [productos, setProductos] = useState([]);

  useEffect(() => {
    axios
      .get("http://127.0.0.1:8000/api/productos")
      .then((res) => {

        // 🔥 Aquí cambiaremos el filtro cuando me digas el tipo de oferta
        const ofertas = res.data.filter(p => p.oferta === true);

        setProductos(ofertas);
      })
      .catch((err) => console.error("Error cargando ofertas:", err));
  }, []);

  return (
    <div className="container my-5">
      <h2 className="text-center text-danger fw-bold mb-4">Ofertas Especiales</h2>
      <p className="text-center text-muted">¡Aprovecha los descuentos disponibles por tiempo limitado!</p>

      <div className="row g-4 mt-4">
        {productos.length > 0 ? (
          productos.map((p) => (
            <div key={p.id} className="col-md-4">
              <div className="card shadow-sm">
                <Link to={`/productos/${p.id}`}>
                  <img src={p.imagenUrl} alt={p.nombre} className="card-img-top" />
                </Link>

                <div className="card-body">
                  <h5 className="card-title">{p.nombre}</h5>
                  <p className="card-text">{p.descripcion}</p>
                  <h5 className="text-danger fw-bold">{p.precio} €</h5>

                  <Link to={`/productos/${p.id}`} className="btn btn-outline-danger w-100">
                    Ver Producto
                  </Link>
                </div>
              </div>
            </div>
          ))
        ) : (
          <p className="text-center">No hay ofertas disponibles en este momento</p>
        )}
      </div>
    </div>
  );
}
