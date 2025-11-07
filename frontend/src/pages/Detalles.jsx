import { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
import axios from "axios";
import { useNavigate } from "react-router-dom";

export default function Detalles({ usuario, carrito, setCarrito }) {
  const { id } = useParams();
  const [producto, setProducto] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [resenas, setResenas] = useState([]);
  const [comentario, setComentario] = useState("");
  const [puntuacion, setPuntuacion] = useState(5);
  const [respuestaTexto, setRespuestaTexto] = useState({});
  const navigate = useNavigate();

  useEffect(() => {
    axios
      .get(`http://127.0.0.1:8000/api/productos/${id}`)
      .then((res) => {
        setProducto(res.data);
        setLoading(false);
      })
      .catch((err) => {
        console.error("Error cargando producto:", err);
        setError("No se pudo cargar el producto");
        setLoading(false);
      });
  }, [id]);

  useEffect(() => {
    axios
      .get(`http://127.0.0.1:8000/api/productos/${id}/resenas`)
      .then((res) => setResenas(res.data))
      .catch((err) => console.log("Error cargando reseñas:", err));
  }, [id]);

  // 🔹 Agregar al carrito con cantidad elegida
  const agregarAlCarrito = () => {
    if (!usuario) {
      alert("Debes iniciar sesión para comprar 🛒");
      navigate("/login");
      return;
    }

    if (!producto) return;

    const cantidadStr = prompt(
      `¿Cuántas unidades de "${producto.nombre}" deseas añadir al carrito?`,
      "1"
    );

    const cantidad = parseInt(cantidadStr, 10);

    if (isNaN(cantidad) || cantidad <= 0) {
      alert("Por favor, introduce una cantidad válida.");
      return;
    }

    const existente = carrito.find((p) => p.id === producto.id);
    if (existente) {
      // Si ya existe, sumamos a la cantidad actual
      setCarrito(
        carrito.map((p) =>
          p.id === producto.id
            ? { ...p, cantidad: p.cantidad + cantidad }
            : p
        )
      );
    } else {
      // Si no existe, lo agregamos con la cantidad seleccionada
      setCarrito([...carrito, { ...producto, cantidad }]);
    }

    alert(
      `🛍️ Se han añadido ${cantidad} ${producto.nombre}${
        cantidad > 1 ? "s" : ""
      } al carrito.`
    );
  };

  const enviarResena = async () => {
    if (!usuario) {
      alert("Debes iniciar sesión para dejar una reseña");
      navigate("/login");
      return;
    }

    try {
      await axios.post("http://127.0.0.1:8000/api/resenas", {
        usuario_id: usuario.id,
        producto_id: producto.id,
        comentario,
        puntuacion
      });

      alert("✅ Reseña enviada correctamente");
      setComentario("");
      setPuntuacion(0);

      // Recargar reseñas
      const res = await axios.get(`http://127.0.0.1:8000/api/productos/${producto.id}/resenas`);
      setResenas(res.data);

    } catch (err) {
      console.error(err);
      alert("❌ Error al enviar reseña");
    }
  };

  const enviarComentario = async (resenaId) => {
    if (!usuario) {
      alert("Debes iniciar sesión para comentar.");
      navigate("/login");
      return;
    }

    const texto = respuestaTexto[resenaId];
    if (!texto || texto.trim() === "") return;

    try {
      await axios.post(`http://127.0.0.1:8000/api/resenas/${resenaId}/comentarios`, {
        usuario_id: usuario.id,
        texto,
      });

      // Limpiar input
      setRespuestaTexto({ ...respuestaTexto, [resenaId]: "" });

      // Recargar reseñas
      const res = await axios.get(`http://127.0.0.1:8000/api/productos/${producto.id}/resenas`);
      setResenas(res.data);

    } catch (e) {
      console.error(e);
      alert("❌ Error al enviar comentario");
    }
  };

  if (loading) return <p className="text-center mt-5">Cargando...</p>;
  if (error) return <p className="text-center text-danger mt-5">{error}</p>;

  return (
    <div className="detalles-container container mt-5">
      <div className="card shadow-lg p-4">
        <div className="row g-4 align-items-center">
          <div className="col-md-5 text-center">
            <img
              src={producto.imagenUrl}
              alt={producto.nombre}
              className="img-fluid rounded"
              style={{ maxHeight: "400px", objectFit: "cover" }}
            />
          </div>

          <div className="col-md-7">
            <h2 className="mb-3">{producto.nombre}</h2>
            <p className="text-muted">{producto.descripcion}</p>
            <p>
              <strong>Categoría:</strong> {producto.categoria}
            </p>
            <h4 className="text-success mb-4">{producto.precio} €</h4>

            <button onClick={agregarAlCarrito} className="btn btn-primary me-3">
              Agregar al carrito 🛍️
            </button>
            <Link to="/productos" className="btn btn-secondary">
              Volver al catálogo
            </Link>
          </div>
        </div>
      </div>
      <div className="container mt-4">
        <h3>Reseñas</h3>
        <hr />

        {resenas.length === 0 && <p>No hay reseñas todavía.</p>}

        {resenas.map((r) => (
          <div key={r.id} className="border rounded p-3 mb-3">
            <p><strong>{r.usuario}</strong> ⭐ {r.puntuacion}/5</p>
            <p>{r.comentario}</p>
            <small className="text-muted">{r.fecha}</small>

            {/* Mostrar comentarios */}
            {r.respuestas.length > 0 && (
              <div className="mt-3 ps-3 border-start">
                {r.respuestas.map((c, index) => (
                  <p key={index}>
                    <strong>{c.usuario}</strong>: {c.texto}
                    <br />
                    <small className="text-muted">{c.fecha}</small>
                  </p>
                ))}
              </div>
            )}

            {/* Formulario para añadir comentario */}
            {usuario && (
              <div className="mt-2">
                <textarea
                  className="form-control mb-2"
                  placeholder="Escribe una respuesta..."
                  value={respuestaTexto[r.id] || ""}
                  onChange={(e) =>
                    setRespuestaTexto({ ...respuestaTexto, [r.id]: e.target.value })
                  }
                ></textarea>
                <button
                  className="btn btn-sm btn-outline-primary"
                  onClick={() => enviarComentario(r.id)}
                >
                  Responder 💬
                </button>
              </div>
            )}
          </div>
        ))}

        <h4 className="mt-4">Escribe tu reseña</h4>

        <select
          value={puntuacion}
          onChange={(e) => setPuntuacion(e.target.value)}
          className="form-select w-auto mb-2"
        >
          {[1, 2, 3, 4, 5].map((n) => (
            <option key={n} value={n}>
              {n} ⭐
            </option>
          ))}
        </select>

        <textarea
          className="form-control mb-2"
          value={comentario}
          onChange={(e) => setComentario(e.target.value)}
          placeholder="Escribe tu opinión..."
        ></textarea>

        <button className="btn btn-primary" onClick={enviarResena}>
          Publicar reseña
        </button>
      </div>
    </div>
  );
}
