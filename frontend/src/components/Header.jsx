import { Link, useNavigate } from "react-router-dom";

const Header = () => {
  return (
    <header className="custom-header">
      <Link className="navbar-brand custom-logo" to="/">Señor Turrón</Link>
    </header>
  );
};

export default Header;