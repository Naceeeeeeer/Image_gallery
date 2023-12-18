<html lang="en">
<head>
    
</head>
<body>
<div class="input-container">
  <input type="text" placeholder="Add Item">
  <button class="button">Add</button>
</div>
<button class="button">
    <span class="button-content">Simple  Search </span>
</button>
<button class="button">
    <span class="button-content">RF Search</span>
</button>
</body>

<style>

.input-container {
  display: flex;
  background: white;
  border-radius: 1rem;
  background: linear-gradient(135deg, #23272F 0%, #14161a 100%);
  box-shadow: 10px 10px 20px #0e1013, -10px -10px 40px #383e4b;
  padding: 0.3rem;
  gap: 0.3rem;
}

.input-container input {
  border-radius: 0.8rem 0 0 0.8rem;
  background: #23272F;
  box-shadow: inset 5px 5px 10px #0e1013, inset -5px -5px 10px #383e4b, 0px 0px 100px rgba(255, 212, 59, 0), 0px 0px 100px rgba(255, 102, 0, 0);
  width: 100%;
  flex-basis: 75%;
  padding: 1rem;
  border: none;
  border: 1px solid transparent;
  color: white;
  transition: all 0.2s ease-in-out;
}

.input-container input:focus {
  border: 1px solid #FFD43B;
  outline: none;
  box-shadow: inset 0px 0px 10px rgba(255, 102, 0, 0.5), inset 0px 0px 10px rgba(255, 212, 59, 0.5), 0px 0px 100px rgba(255, 212, 59, 0.5), 0px 0px 100px rgba(255, 102, 0, 0.5);
}

.input-container button {
  flex-basis: 25%;
  padding: 1rem;
  background: linear-gradient(135deg, rgb(255, 212, 59) 0%, rgb(255, 102, 0) 100%);
  box-shadow: 0px 0px 1px rgba(255, 212, 59, 0.5), 0px 0px 1px rgba(255, 102, 0, 0.5);
  font-weight: 900;
  letter-spacing: 0.3rem;
  text-transform: uppercase;
  color: #23272F;
  border: none;
  width: 100%;
  border-radius: 0 1rem 1rem 0;
  transition: all 0.2s ease-in-out;
}

.input-container button:hover {
  background: linear-gradient(135deg, rgb(255, 212, 59) 50%, rgb(255, 102, 0) 100%);
  box-shadow: 0px 0px 100px rgba(255, 212, 59, 0.5), 0px 0px 100px rgba(255, 102, 0, 0.5);
}

@media (max-width: 500px) {
  .input-container {
    flex-direction: column;
  }

  .input-container input {
    border-radius: 0.8rem;
  }

  .input-container button {
    padding: 1rem;
    border-radius: 0.8rem;
  }
}
.button {
  position: relative;
  overflow: hidden;
  height: 3rem;
  padding: 0 2rem;
  border-radius: 1.5rem;
  background: #3d3a4e;
  background-size: 400%;
  color: #fff;
  border: none;
}

.button:hover::before {
  transform: scaleX(1);
}

.button-content {
  position: relative;
  z-index: 1;
}

.button::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  transform: scaleX(0);
  transform-origin: 0 50%;
  width: 100%;
  height: inherit;
  border-radius: inherit;
  background: linear-gradient(
    82.3deg,
    rgba(150, 93, 233, 1) 10.8%,
    rgba(99, 88, 238, 1) 94.3%
  );
  transition: all 0.475s;
}
</style>
</html>