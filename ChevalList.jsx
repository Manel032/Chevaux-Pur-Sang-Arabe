// ChevalList.jsx - React component for displaying and filtering horses
const { useState, useEffect } = React;

function ChevalList({ onViewDetail, onEdit, onDelete, refreshTrigger }) {
    const [chevaux, setChevaux] = useState([]);
    const [filters, setFilters] = useState({
        race: '',
        sexe: '',
        search: ''
    });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchChevaux();
    }, [filters, refreshTrigger]);

    const fetchChevaux = async () => {
        setLoading(true);
        try {
            const queryParams = new URLSearchParams(filters).toString();
            const res = await axios.get(`api.php?action=get_chevaux&${queryParams}`);
            if (res.data.success) {
                setChevaux(res.data.data);
            }
        } catch (err) {
            console.error("Erreur lors de la récupération des chevaux", err);
        } finally {
            setLoading(false);
        }
    };

    const handleFilterChange = (e) => {
        const { name, value } = e.target;
        setFilters(prev => ({
            ...prev,
            [name]: value
        }));
    };

    const getBreedBadgeClass = (race) => {
        if (race === 'Pur-Sang Arabe') return 'badge-arabe';
        if (race === 'Barbe') return 'badge-barbe';
        return 'badge-arabe-barbe';
    };

    return (
        <div>
            {/* Filters Bar */}
            <div className="glass-panel filters-bar">
                <div className="search-input-wrapper">
                    <input 
                        type="text" 
                        name="search" 
                        className="form-control" 
                        value={filters.search} 
                        onChange={handleFilterChange} 
                        placeholder="🔍 Rechercher par nom, robe ou propriétaire..." 
                    />
                </div>

                <div style={{display:'flex', gap:'1rem', flexWrap:'wrap'}}>
                    <div>
                        <select name="race" className="form-control" value={filters.race} onChange={handleFilterChange}>
                            <option value="">Tous les types de races</option>
                            <option value="Pur-Sang Arabe">Pur-Sang Arabe</option>
                            <option value="Barbe">Le Barbe</option>
                            <option value="Arabe-Barbe">Arabe-Barbe</option>
                        </select>
                    </div>

                    <div>
                        <select name="sexe" className="form-control" value={filters.sexe} onChange={handleFilterChange}>
                            <option value="">Tous les sexes</option>
                            <option value="Mâle">Mâles</option>
                            <option value="Femelle">Femelles</option>
                        </select>
                    </div>
                </div>
            </div>

            {loading ? (
                <div style={{textAlign: 'center', padding: '3rem'}}>
                    <div className="stat-icon" style={{margin:'0 auto 1.5rem auto'}}>⏳</div>
                    <h3>Recherche dans le Studbook national...</h3>
                </div>
            ) : chevaux.length === 0 ? (
                <div className="glass-panel" style={{textAlign: 'center', padding: '3rem', color: 'var(--text-muted)'}}>
                    <span style={{fontSize: '3rem'}}>🐴</span>
                    <h3 style={{marginTop: '1rem'}}>Aucun cheval trouvé</h3>
                    <p>Modifiez vos filtres ou ajoutez un nouveau cheval pour enrichir le registre.</p>
                </div>
            ) : (
                <div className="cards-grid">
                    {chevaux.map(c => (
                        <div key={c.id} className="glass-panel item-card">
                            <div className="card-image-wrapper">
                                <img 
                                    src={c.image_url ? c.image_url : 'hero_fantasia.webp'} 
                                    alt={c.nom} 
                                    className="card-image" 
                                />
                                <span className={`card-badge ${getBreedBadgeClass(c.race)}`}>
                                    {c.race}
                                </span>
                            </div>
                            
                            <div className="card-details">
                                <h3>{c.nom}</h3>
                                
                                <div className="card-meta-info">
                                    <div className="meta-item">
                                        <span>🧬 Sexe :</span> <strong>{c.sexe}</strong>
                                    </div>
                                    {c.robe && (
                                        <div className="meta-item">
                                            <span>🎨 Robe :</span> <strong>{c.robe}</strong>
                                        </div>
                                    )}
                                </div>

                                <div style={{fontSize:'0.85rem', color:'var(--text-muted)', marginBottom:'1rem'}}>
                                    {c.pere_nom && <p>♂️ Père : <strong>{c.pere_nom}</strong></p>}
                                    {c.mere_nom && <p>♀️ Mère : <strong>{c.mere_nom}</strong></p>}
                                    {c.owner_nom && <p>🏛️ Haras : <strong>{c.owner_nom}</strong></p>}
                                </div>

                                <div className="card-actions">
                                    <button 
                                        className="btn btn-secondary btn-sm" 
                                        style={{flexGrow: 1}}
                                        onClick={() => onViewDetail(c.id)}
                                    >
                                        🌳 Pedigree
                                    </button>
                                    <button 
                                        className="btn btn-secondary btn-sm" 
                                        onClick={() => onEdit(c.id)}
                                        title="Modifier"
                                    >
                                        ✏️
                                    </button>
                                    <button 
                                        className="btn btn-danger btn-sm" 
                                        onClick={() => onDelete(c.id)}
                                        title="Supprimer"
                                    >
                                        🗑️
                                    </button>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

window.ChevalList = ChevalList;
