import React, { useState } from "react";
import { createRoot } from "react-dom/client";
import { Form as BootstrapForm, Button, Spinner } from 'react-bootstrap';
import Modal from './modal';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faCheck, faTimes } from '@fortawesome/free-solid-svg-icons';

const Analyse = () => {
    const [url, setUrl] = useState('');
    const [isAnalyzing, setIsAnalyzing] = useState(false);
    const [progressMessage, setProgressMessage] = useState('');
    const [analysisComplete, setAnalysisComplete] = useState(false);
    const [onError, setOnError] = useState(false);

    const handleClose = () => {
        setIsAnalyzing(false);
        setAnalysisComplete(false);
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setIsAnalyzing(true);
        setAnalysisComplete(false);
        setOnError(false);
        setProgressMessage('Analysis in progress...');

        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ url: url })
        };

        try {
            setTimeout(async () => {
                const response = await fetch(`http://134.209.182.173:8000/analyse`, requestOptions);
                const data = await response.json();
                if (!response.ok) {
                    setTimeout(async () => {
                        setOnError(true);
                        setIsAnalyzing(false);
                        setAnalysisComplete(true);
                        setProgressMessage(data.error);
                    }, 2000);
                }
                if (response.ok) {
                    setTimeout(async () => {
                        setProgressMessage('Analysis completed.');
                        setProgressMessage(`The report was sent to the email : ${data.email}. \n You can also find it in the My reports section.`);
                        const fetchData = await fetch('http://127.0.0.1:8000/sendMail', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ report: data.report }) });
                        setIsAnalyzing(false);
                        setAnalysisComplete(true);
                    }, 2000);
                }

            }, 1000);
        } catch (error) {
            setIsAnalyzing(false);
            setProgressMessage(error.message);
        }
    };

    return (
        <>
            <div className="form-container centered-form">
                <h2>Analyze request</h2>
                <BootstrapForm onSubmit={handleSubmit} className="custom-form">
                    <BootstrapForm.Group controlId="formUrl">
                        <BootstrapForm.Label>GitHub URL to analyze :</BootstrapForm.Label>
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

                        Analyze PHP code
                    </Button>
                </BootstrapForm>
            </div>

            <Modal show={isAnalyzing || analysisComplete} onClose={handleClose}>
                <div style={{ color: 'black', textAlign: 'center' }}>
                    {
                        isAnalyzing
                            ? <Spinner animation="border" role="status" style={{ marginBottom: '10px' }}>
                                <span className="visually-hidden">Loading...</span>
                            </Spinner>
                            : analysisComplete && !onError
                                ? <FontAwesomeIcon icon={faCheck} size="3x" color="green" />
                                : onError
                                    ? <FontAwesomeIcon icon={faTimes} size="3x" color="red" />
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
