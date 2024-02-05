import React, { useState } from "react";
import { createRoot } from "react-dom/client";

const Login = () => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');

    const handleSubmit = async (event) => {
        event.preventDefault();

        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: email, password: password})
        };

        try {
            const response = await fetch('http://127.0.0.1:8000/login',
                requestOptions
            );
            const data = await response.json();
            console.log('Analyse effectué avec succès:', data);
        } catch (error) {
            console.error('Erreur lors de l\'envoi du formulaire:', error);
        }
    };

    return (
        <div className="form-container">
            <h2>Registration</h2>
            <form onSubmit={handleSubmit}>
                <label>
                    Email :
                    <input
                        type="text"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                    />
                </label>
                <label>
                    Password :
                    <input
                        type="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        required
                    />
                </label>
                <button type="submit">Register</button>
            </form>
        </div>
    );
};

const formHtml = document.getElementById('login');
const form = createRoot(formHtml);
form.render(<Login />);
