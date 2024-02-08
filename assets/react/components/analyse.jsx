import React, { useState } from "react";
import { createRoot } from "react-dom/client";
import { Form as BootstrapForm, Button, Spinner } from 'react-bootstrap';
import Modal from './modal';

const Analyse = () => {
    const [url, setUrl] = useState('');
    const [isAnalyzing, setIsAnalyzing] = useState(false);
    const [progressMessage, setProgressMessage] = useState('');
    const [analysisComplete, setAnalysisComplete] = useState(false);

    const handleClose = () => {
        setIsAnalyzing(false);
        setAnalysisComplete(false);
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setIsAnalyzing(true);
        setAnalysisComplete(false);
        setProgressMessage('Analyse en cours...');

        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ url: url })
        };

        try {
            setTimeout(async () => {
                const response = await fetch('http://127.0.0.1:8000/analyse', requestOptions);
                if (!response.ok) throw new Error('Réponse réseau non ok');
                setTimeout(async () => {
                    const data = await response.json();
                    console.log('Analyse effectuée avec succès:', data);
                    setProgressMessage('Analyse terminée.');
                    setProgressMessage(`Le rapport a été envoyé au mail : ${data.email}. \n Vous pouvez également le retrouver sur la section My reports.`);
                    const fetchData = await fetch('http://127.0.0.1:8000/sendMail', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ report: data.report }) });
                    setIsAnalyzing(false);
                    setAnalysisComplete(true);
                    // fetch vers sendmail
                }, 2000);
            }, 1000);
        } catch (error) {
            console.error('Erreur lors de l\'envoi du formulaire:', error);
            setIsAnalyzing(false);
            setProgressMessage('Erreur lors de l\'analyse. Veuillez réessayer.');
        }
    };

    return (
        <>
            <div className="form-container centered-form">
                <h2>Formulaire de demande d'analyse</h2>
                <BootstrapForm onSubmit={handleSubmit} className="custom-form">
                    <BootstrapForm.Group controlId="formUrl">
                        <BootstrapForm.Label>URL GitHub à analyser :</BootstrapForm.Label>
                        <BootstrapForm.Control
                            type="text"
                            value={url}
                            onChange={(e) => setUrl(e.target.value)}
                            required
                        />
                    </BootstrapForm.Group>
                    <Button
                        type="submit"
                        className="mt-3 purple-btn"
                    >
                        Analyser le code PHP
                    </Button>
                </BootstrapForm>
            </div>

            <Modal show={isAnalyzing || analysisComplete} onClose={handleClose}>
                <div style={{ color: 'black', textAlign: 'center' }}>
                    {
                        isAnalyzing
                            ? <Spinner animation="border" role="status" style={{ marginBottom: '10px' }}>
                                <span className="visually-hidden">Chargement...</span>
                            </Spinner>
                            : analysisComplete
                                ? <i className="fas fa-check" style={{ fontSize: '24px', color: 'green', marginBottom: '10px' }}></i>
                                : null
                    }
                    <div>{progressMessage}</div>
                    {analysisComplete && (
                        <Button
                            variant="secondary"
                            onClick={handleClose}
                            style={{ marginTop: '20px' }}
                        >
                            Close
                        </Button>
                    )}
                </div>
            </Modal>
        </>
    );
};

const analyseHtml = document.getElementById('analyse');
const analyse = createRoot(analyseHtml);
analyse.render(<Analyse />);
