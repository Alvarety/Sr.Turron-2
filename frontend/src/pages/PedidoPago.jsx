// import { useEffect, useState } from "react";
// import { useParams } from "react-router-dom";
// import { authFetch } from "../pages/admin/utils/api";
// import PagoStripe from "../components/PagoStripe";

// export default function PedidoPago() {
//   const { id } = useParams();
//   const [pedido, setPedido] = useState(null);

//   useEffect(() => {
//     async function cargarPedido() {
//       const res = await authFetch(`http://127.0.0.1:8000/api/pedidos/${id}`);
//       const data = await res.json();
//       setPedido(data);
//     }
//     cargarPedido();
//   }, [id]);

//   if (!pedido) return <p>Cargando pedido...</p>;

//   return (
//     <div className="container mt-4">
//       <h2>Pago del pedido #{pedido.id}</h2>
//       <p>Total: {pedido.total} €</p>

//       <h3>Introducir tarjeta</h3>
//       <PagoStripe pedidoId={pedido.id} />
//     </div>
//   );
// }
