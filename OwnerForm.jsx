// OwnerForm.jsx - React component for adding/editing owners
const { useState, useEffect } = React;

function OwnerForm({ ownerId, onSave, onCancel }) {
    const [formData, setFormData] = useState({
        nom: '',
        telephone: '',
        email: '',
        adresse: ''
    });
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        if (ownerId) {
            loadOwner();
        }
    }, [ownerId]);

    const loadOwner = async () => {
        setLoading(true);
        try {
            const res = await axios.get(`api.php?action=get_owner&id=${ownerId}`);
            if (res.data.success) {
                const owner = res.data.data;
                setFormData({
                    nom: owner.nom,
                    telephone: owner.telephone || '',
                    email: owner.email || '',
                    adresse: owner.adresse || ''
                });
            }
        } catch (err) {
            console.error("Erreur de chargement de l'éleveur", err);
            setError("Impossible de charger les données du propriétaire.");
        } finally {
            setLoading(false);
        }
    };

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');

        if (!formData.nom.trim()) {
            setError("Le nom de l'établissement ou de la personne est requis.");
            return;
        }

        setLoading(true);
        try {
            let res;
            if (ownerId) {
                res = await axios.post(`api.php?action=update_owner&id=${ownerId}`, formData);
            } else {
                res = await axios.post('api.php?action=create_owner', formData);
            }

            if (res.data.success) {
                onSave();
            } else {
                setError(res.data.message || "Une erreur s'est produite.");
            }
        } catch (err) {
            console.error("Erreur d'enregistrement de l'éleveur", err);
            setError("Erreur de communication avec le serveur.");
        } finally {
            setLoading(false);
        }
    };

    if (loading && ownerId) return <div style={{padding:'2rem', textAlign:'center'}}>Chargement du formulaire...</div>;

    return (
        <form onSubmit={handleSubmit} className="glass-panel" style={{padding: '2rem', borderTop: '2px solid var(--accent-gold)'}}>
            <h3 style={{marginBottom: '1.5rem', color: 'var(--accent-gold)'}}>
                {ownerId ? '✏️ Modifier la Fiche Propriétaire' : '🏢 Ajouter un Nouveau Propriétaire / Haras'}
            </h3>

            {error && (
                <div style={{
                    padding: '0.75rem', 
                    background: 'rgba(255, 77, 77, 0.15)', 
                    color: 'var(--danger)', 
                    border: '1px solid var(--danger)', 
                    borderRadius: '8px', 
                    marginBottom: '1.5rem',
                    fontSize: '0.9rem'
                }}>
                    ⚠️ {error}
                </div>
            )}

            <div className="form-container" style={{gridTemplateColumns:'1fr'}}>
                {/* Name */}
                <div className="form-group">
                    <label className="form-label">Nom Complet / Raison Sociale du Haras</label>
                    <input 
                        type="text" 
                        name="nom" 
                        className="form-control" 
                        value={formData.nom} 
                        onChange={handleChange}
                        placeholder="Ex: Haras National de Sidi Thabet" 
                        required
                    />
                </div>

                {/* Telephone */}
                <div className="form-group">
                    <label className="form-label">Téléphone de Contact</label>
                    <input 
                        type="tel" 
                        name="telephone" 
                        className="form-control" 
                        value={formData.telephone} 
                        onChange={handleChange}
                        placeholder="Ex: 71 546 222" 
                    />
                </div>

                {/* Email */}
                <div className="form-group">
                    <label className="form-label">Adresse Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        className="form-control" 
                        value={formData.email} 
                        onChange={handleChange}
                        placeholder="Ex: contact@haras.tn" 
                    />
                </div>

                {/* Address */}
                <div className="form-group">
                    <label className="form-label">Adresse Physique</label>
                    <input 
                        type="text" 
                        name="adresse" 
                        className="form-control" 
                        value={formData.adresse} 
                        onChange={handleChange}
                        placeholder="Ex: Sidi Thabet, Ariana, Tunisie" 
                    />
                </div>
            </div>

            <div style={{display:'flex', gap:'1rem', marginTop:'2rem', justifyContent:'flex-end'}}>
                <button type="button" className="btn btn-secondary" onClick={onCancel} disabled={loading}>
                    Annuler
                </button>
                <button type="submit" className="btn btn-primary" disabled={loading}>
                    {loading ? 'Sauvegarde...' : '💾 Enregistrer'}
                </button>
            </div>
        </form>
    );
}

window.OwnerForm = OwnerForm;
