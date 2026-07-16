// ChevalForm.jsx - React form component for creating and editing horses
const { useState, useEffect } = React;

function ChevalForm({ horseId, onSave, onCancel }) {
    const [formData, setFormData] = useState({
        nom: '',
        race: 'Pur-Sang Arabe',
        sexe: 'Mâle',
        date_naissance: '',
        robe: '',
        pere_id: '',
        mere_id: '',
        owner_id: '',
        image_url: 'pur_sang_arabe.webp'
    });

    const [peres, setPeres] = useState([]);
    const [meres, setMeres] = useState([]);
    const [owners, setOwners] = useState([]);
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        loadDropdownData();
        if (horseId) {
            loadHorseData();
        }
    }, [horseId]);

    const loadDropdownData = async () => {
        try {
            const resParents = await axios.get(`api.php?action=get_parents_lists${horseId ? `&exclude_id=${horseId}` : ''}`);
            const resOwners = await axios.get('api.php?action=get_owners');
            
            if (resParents.data.success) {
                setPeres(resParents.data.peres);
                setMeres(resParents.data.meres);
            }
            if (resOwners.data.success) {
                setOwners(resOwners.data.data);
            }
        } catch (err) {
            console.error("Erreur de chargement des listes déroulantes", err);
        }
    };

    const loadHorseData = async () => {
        setLoading(true);
        try {
            const res = await axios.get(`api.php?action=get_cheval&id=${horseId}`);
            if (res.data.success) {
                const horse = res.data.data;
                setFormData({
                    nom: horse.nom,
                    race: horse.race,
                    sexe: horse.sexe,
                    date_naissance: horse.date_naissance || '',
                    robe: horse.robe || '',
                    pere_id: horse.pere_id || '',
                    mere_id: horse.mere_id || '',
                    owner_id: horse.owner_id || '',
                    image_url: horse.image_url || 'pur_sang_arabe.webp'
                });
            }
        } catch (err) {
            console.error("Erreur de chargement du cheval", err);
            setError("Impossible de charger les données de ce cheval.");
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
        
        // Front-end validations
        if (!formData.nom.trim()) {
            setError("Le nom est obligatoire.");
            return;
        }
        if (!formData.race) {
            setError("La race est obligatoire.");
            return;
        }
        if (!formData.sexe) {
            setError("Le sexe est obligatoire.");
            return;
        }

        setLoading(true);
        try {
            let res;
            if (horseId) {
                res = await axios.post(`api.php?action=update_cheval&id=${horseId}`, formData);
            } else {
                res = await axios.post('api.php?action=create_cheval', formData);
            }

            if (res.data.success) {
                onSave();
            } else {
                setError(res.data.message || "Une erreur est survenue lors de l'enregistrement.");
            }
        } catch (err) {
            console.error("Erreur de sauvegarde", err);
            setError("Erreur de communication avec le serveur.");
        } finally {
            setLoading(false);
        }
    };

    if (loading && horseId) return <div style={{padding:'2rem', textAlign:'center'}}>Chargement du formulaire...</div>;

    return (
        <form onSubmit={handleSubmit} className="glass-panel" style={{padding: '2rem', borderTop: '2px solid var(--accent-gold)'}}>
            <h3 style={{marginBottom: '1.5rem', color: 'var(--accent-gold)'}}>
                {horseId ? '✏️ Modifier la Fiche' : '➕ Enregistrer un Nouveau Cheval'}
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

            <div className="form-container">
                {/* Name */}
                <div className="form-group">
                    <label className="form-label">Nom du Cheval</label>
                    <input 
                        type="text" 
                        name="nom" 
                        className="form-control" 
                        value={formData.nom} 
                        onChange={handleChange}
                        placeholder="Ex: Nassim" 
                        required
                    />
                </div>

                {/* Breed */}
                <div className="form-group">
                    <label className="form-label">Race</label>
                    <select name="race" className="form-control" value={formData.race} onChange={handleChange} required>
                        <option value="Pur-Sang Arabe">Pur-Sang Arabe</option>
                        <option value="Barbe">Barbe</option>
                        <option value="Arabe-Barbe">Arabe-Barbe</option>
                    </select>
                </div>

                {/* Gender */}
                <div className="form-group">
                    <label className="form-label">Sexe</label>
                    <select name="sexe" className="form-control" value={formData.sexe} onChange={handleChange} required>
                        <option value="Mâle">Mâle</option>
                        <option value="Femelle">Femelle</option>
                    </select>
                </div>

                {/* Color (Robe) */}
                <div className="form-group">
                    <label className="form-label">Robe (Couleur)</label>
                    <input 
                        type="text" 
                        name="robe" 
                        className="form-control" 
                        value={formData.robe} 
                        onChange={handleChange}
                        placeholder="Ex: Gris, Alezan, Bai, Noir" 
                    />
                </div>

                {/* Birth Date */}
                <div className="form-group">
                    <label className="form-label">Date de Naissance</label>
                    <input 
                        type="date" 
                        name="date_naissance" 
                        className="form-control" 
                        value={formData.date_naissance} 
                        onChange={handleChange} 
                    />
                </div>

                {/* Image Illustration */}
                <div className="form-group">
                    <label className="form-label">Image d'illustration</label>
                    <select name="image_url" className="form-control" value={formData.image_url} onChange={handleChange}>
                        <option value="pur_sang_arabe.webp">Pur-Sang Arabe (Par défaut)</option>
                        <option value="barbe_tunisien.webp">Barbe Tunisien</option>
                        <option value="hero_fantasia.webp">Spectacle de Fantasia</option>
                    </select>
                </div>

                {/* Sire (Father) */}
                <div className="form-group">
                    <label className="form-label">Père (Sire)</label>
                    <select name="pere_id" className="form-control" value={formData.pere_id} onChange={handleChange}>
                        <option value="">-- Père Inconnu / Non Enregistré --</option>
                        {peres.map(p => (
                            <option key={p.id} value={p.id}>{p.nom} ({p.race})</option>
                        ))}
                    </select>
                </div>

                {/* Dam (Mother) */}
                <div className="form-group">
                    <label className="form-label">Mère (Dam)</label>
                    <select name="mere_id" className="form-control" value={formData.mere_id} onChange={handleChange}>
                        <option value="">-- Mère Inconnue / Non Enregistrée --</option>
                        {meres.map(m => (
                            <option key={m.id} value={m.id}>{m.nom} ({m.race})</option>
                        ))}
                    </select>
                </div>

                {/* Owner */}
                <div className="form-group full-width">
                    <label className="form-label">Propriétaire / Haras de rattachement</label>
                    <select name="owner_id" className="form-control" value={formData.owner_id} onChange={handleChange}>
                        <option value="">-- Aucun Propriétaire Associé --</option>
                        {owners.map(o => (
                            <option key={o.id} value={o.id}>{o.nom} - {o.adresse}</option>
                        ))}
                    </select>
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

window.ChevalForm = ChevalForm;
