import React, { useState } from "react";
import { createRoot } from "react-dom/client";
import { Form as BootstrapForm, Button, Spinner } from 'react-bootstrap';
import Modal from './modal'; // Assurez-vous que le chemin vers votre composant Modal est correct

const Analyse = () => {
    const [url, setUrl] = useState('');
    const [isAnalyzing, setIsAnalyzing] = useState(false);
    const [progressMessages, setProgressMessages] = useState([]);

    const addProgressMessage = (message) => {
        setProgressMessages((prevMessages) => [...prevMessages, message]);
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setIsAnalyzing(true);
        setProgressMessages(['Envoi de la demande d\'analyse...']); // Réinitialisez les messages de progression

        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ url: url })
        };

        // Simulation de l'analyse avec des étapes de progression
        setTimeout(() => {
            addProgressMessage('Connexion au serveur...');
            
            setTimeout(async () => {
                // Simuler l'envoi de la requête
                addProgressMessage('Analyse du dépôt GitHub en cours...');

                setTimeout(async () => {
                    // Simuler la réponse du serveur
                    try {
                        const response = await fetch('http://127.0.0.1:8000/analyse', requestOptions);
                        if (!response.ok) throw new Error('Réponse réseau non ok');

                        addProgressMessage('Traitement des données...');
                        
                        setTimeout(async () => {
                            // Simuler le traitement final et l'affichage du résultat
                            const data = await response.json();
                            console.log('Analyse effectuée avec succès:', data);
                            addProgressMessage('Analyse terminée.');
                            setIsAnalyzing(false);
                        }, 2000); // Temps simulé pour le traitement final
                    } catch (error) {
                        console.error('Erreur lors de l\'envoi du formulaire:', error);
                        addProgressMessage('Erreur lors de l\'analyse. Veuillez réessayer.');
                        setIsAnalyzing(false);
                    }
                }, 2000); // Temps simulé pour la réponse du serveur
            }, 1000); // Temps simulé pour la connexion
        }, 500); // Démarrage immédiat
    };

    return (
        <>
            <div className="form-container centered-form">
                {/* Le reste du composant reste inchangé */}
            </div>

            <Modal show={isAnalyzing}>
                <div style={{ color: 'black', textAlign: 'center' }}>
                    <Spinner animation="border" role="status" style={{ marginBottom: '10px' }}>
                        <span className="visually-hidden">Chargement...</span>
                    </Spinner>
                    <div>
                        {progressMessages.map((msg, index) => (
                            <div key={index}>{msg}</div>
                        ))}
                    </div>
                </div>
            </Modal>
        </>
    );
};

const analyseHtml = document.getElementById('analyse');
const analyse = createRoot(analyseHtml);
analyse.render(<Analyse />);
