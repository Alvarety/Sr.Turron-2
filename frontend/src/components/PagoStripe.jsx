// import { loadStripe } from "@stripe/stripe-js";

// export default function PagoStripe({ pedidoId }) {

//   const iniciarPago = async () => {
//     const stripe = await loadStripe(import.meta.env.VITE_STRIPE_PUBLIC_KEY);

//     const res = await fetch("http://127.0.0.1:8000/api/stripe/create-checkout", {
//       method: "POST",
//       headers: { "Content-Type": "application/json" },
//       body: JSON.stringify({ pedidoId }),
//     });

//     const data = await res.json();

//     await stripe.redirectToCheckout({
//       sessionId: data.id,
//     });
//   };

//   return (
//     <button className="btn btn-success mt-3" onClick={iniciarPago}>
//       💳 Pagar ahora
//     </button>
//   );
// }
