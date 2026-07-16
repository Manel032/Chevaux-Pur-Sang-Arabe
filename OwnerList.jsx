// OwnerList.jsx - React component for displaying list of owners
const { useState, useEffect } = React;

function OwnerList({ onEdit, onDelete, refreshTrigger }) {
    const [owners, setOwners] = useState([]);
    const [selectedOwner, setSelectedOwner] = useState(null);
    const [ownedHorses, setOwnedHorses] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchOwners();
    }, [refreshTrigger]);

    const fetchOwners = async () => {
        setLoading(true);
        try {
            const res = await axios.get('api.php?action=get_owners');
            if (res.data.success) {
                setOwners(res.data.data);
            }
        } catch (err) {
            console.error("Erreur de chargement des propriétaires", err);
        } finally {
            setLoading(false);
        }
    };

    const handleViewProfile = async (owner) => {
        try {
            const res = await axios.get(`api.php?action=get_owner&id=${owner.id}`);
            if (res.data.success) {
                setSelectedOwner(res.data.data);
                setOwnedHorses(res.data.horses);
            }
        } catch (err) {
            console.error("Erreur profil propriétaire", err);
        }
    };

    if (loading) return <div style={{textAlign:'center', padding:'2rem'}}>Chargement des éleveurs...</div>;

    return (
        <div>
            {owners.length === 0 ? (
                <div className="glass-panel" style={{textAlign: 'center', padding: '3rem', color: 'var(--text-muted)'}}>
                    <span style={{fontSize: '3rem'}}>🏛️</span>
                    <h3 style={{marginTop: '1rem'}}>Aucun éleveur ou haras enregistré</h3>
                    <p>Enregistrez un nouveau haras ou propriétaire pour l'associer aux chevaux.</p>
                </div>
            ) : (
                <div className="glass-panel" style={{padding: '1.5rem'}}>
                    <div className="table-responsive">
                        <table className="custom-table">
                            <thead>
                                <tr>
                                    <th>Nom / Établissement</th>
                                    <th>Téléphone</th>
                                    <th>Email</th>
                                    <th>Adresse</th>
                                    <th style={{textAlign: 'right'}}>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {owners.map(o => (
                                    <tr key={o.id}>
                                        <td>
                                            <a 
                                                href="#" 
                                                style={{color:'var(--accent-gold)', fontWeight:'600'}} 
                                                onClick={(e) => { e.preventDefault(); handleViewProfile(o); }}
                                            >
                                                🏢 {o.nom}
                                            </a>
                                        </td>
                                        <td>{o.telephone || '-'}</td>
                                        <td>{o.email || '-'}</td>
                                        <td>{o.adresse || '-'}</td>
                                        <td style={{textAlign: 'right'}}>
                                            <div style={{display:'inline-flex', gap:'0.5rem'}}>
                                                <button 
                                                    className="btn btn-secondary btn-sm" 
                                                    onClick={() => handleViewProfile(o)}
                                                    title="Voir l'Écurie"
                                                >
                                                    🐎 Écurie
                                                </button>
                                                <button 
                                                    className="btn btn-secondary btn-sm" 
                                                    onClick={() => onEdit(o.id)}
                                                    title="Modifier"
                                                >
                                                    ✏️
                                                </button>
                                                <button 
                                                    className="btn btn-danger btn-sm" 
                                                    onClick={() => onDelete(o.id)}
                                                    title="Supprimer"
                                                >
                                                    🗑️
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}

            {/* Owner Detail modal (Écurie) */}
            {selectedOwner && (
                <div className="modal-overlay" onClick={() => setSelectedOwner(null)}>
                    <div className="modal-content glass-panel" onClick={e => e.stopPropagation()}>
                        <div className="modal-header">
                            <h3>Fiche Établissement : {selectedOwner.nom}</h3>
                            <button className="modal-close" onClick={() => setSelectedOwner(null)}>&times;</button>
                        </div>
                        
                        <div style={{marginBottom:'2rem', paddingBottom:'1.5rem', borderBottom:'1px solid var(--border-light)'}}>
                            <p>📞 <strong>Téléphone :</strong> {selectedOwner.telephone || 'Non renseigné'}</p>
                            <p>✉️ <strong>Email :</strong> {selectedOwner.email || 'Non renseigné'}</p>
                            <p>📍 <strong>Adresse :</strong> {selectedOwner.adresse || 'Non renseignée'}</p>
                        </div>

                        <h4>Chevaux élevés / en pension ({ownedHorses.length})</h4>
                        {ownedHorses.length === 0 ? (
                            <p style={{color:'var(--text-muted)', marginTop:'1rem'}}>Aucun cheval n'est actuellement rattaché à cet établissement.</p>
                        ) : (
                            <div className="table-responsive" style={{marginTop:'1rem'}}>
                                <table className="custom-table">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Race</th>
                                            <th>Sexe</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {ownedHorses.map(h => (
                                            <tr key={h.id}>
                                                <td><strong>{h.nom}</strong></td>
                                                <td>{h.race}</td>
                                                <td>{h.sexe}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
}

window.OwnerList = OwnerList;
