import React, { useEffect, useState } from "react";
import axios from "axios";
import { Link } from "react-router-dom";

export default function Novedades() {
  const [productos, setProductos] = useState([]);

  useEffect(() => {
    axios
      .get("http://127.0.0.1:8000/api/productos")
      .then((res) => {
        // Ordenar por ID (o fecha si existe)
        const recientes = res.data.sort((a, b) => b.id - a.id);
        setProductos(recientes.slice(0, 2)); // Solo 2 últimos
      })
      .catch((err) => console.error("Error cargando novedades:", err));
  }, []);

  return (
    <div className="container my-5">
      <h2 className="text-center text-brown mb-4 fw-bold">Novedades</h2>
      <p className="text-center text-muted mb-4">
        ¡Estos son los turrones más recientes añadidos a nuestra tienda!
      </p>

      <div className="row g-4">
        {productos.length > 0 ? (
          productos.map((p) => (
            <div key={p.id} className="col-md-6">
              <div className="card shadow-sm">
                <Link to={`/productos/${p.id}`}>
                  <img src={p.imagenUrl} alt={p.nombre} className="card-img-top" />
                </Link>

                <div className="card-body">
                  <h5 className="card-title">{p.nombre}</h5>
                  <p className="card-text">{p.descripcion}</p>
                  <h5 className="text-brown fw-bold">{p.precio} €</h5>

                  <Link to={`/productos/${p.id}`} className="btn btn-primary w-100 mt-2">
                    Ver Producto
                  </Link>
                </div>
              </div>
            </div>
          ))
        ) : (
          <p className="text-center">No hay novedades disponibles 😔</p>
        )}
      </div>
    </div>
  );
}
