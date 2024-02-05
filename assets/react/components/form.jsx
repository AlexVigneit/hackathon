import React, { useState } from "react";
import { createRoot } from "react-dom/client";

const Form = () => {
    const [url, setUrl] = useState('');

    const handleSubmit = async (event) => {
        event.preventDefault();

        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ url: url })
        };

        try {
            const response = await fetch('http://127.0.0.1:8000/analyse',
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
            <h2>Formulaire de demande d'analyse</h2>
            <form onSubmit={handleSubmit}>
                <label>
                    URL GitHub à analyser :
                    <input
                        type="text"
                        value={url}
                        onChange={(e) => setUrl(e.target.value)}
                        required
                    />
                </label>
                <button type="submit">Analyser le code PHP</button>
            </form>
        </div>
    );
};

const formHtml = document.getElementById('form');
const form = createRoot(formHtml);
form.render(<Form />);
