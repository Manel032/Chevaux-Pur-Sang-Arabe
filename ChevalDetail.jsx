// ChevalDetail.jsx - React component for Horse Details and Ancestry Pedigree Tree
const { useState, useEffect } = React;

function ChevalDetail({ horseId, onClose }) {
    const [data, setData] = useState(null);
    const [pedigree, setPedigree] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        if (horseId) {
            fetchDetail();
        }
    }, [horseId]);

    const fetchDetail = async () => {
        setLoading(true);
        try {
            const res = await axios.get(`api.php?action=get_cheval&id=${horseId}`);
            if (res.data.success) {
                setData(res.data.data);
                setPedigree(res.data.pedigree);
            }
        } catch (err) {
            console.error("Erreur de chargement des détails du cheval", err);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return (
            <div className="modal-overlay" onClick={onClose}>
                <div className="modal-content glass-panel" style={{textAlign:'center', padding:'3rem'}}>
                    <div className="stat-icon" style={{margin:'0 auto 1.5rem auto'}}>⏳</div>
                    <h3>Chargement des détails généalogiques...</h3>
                </div>
            </div>
        );
    }

    if (!data) return null;

    // Helper to render a node in the pedigree tree
    const renderPedigreeNode = (node, genderLabel) => {
        if (!node) {
            return (
                <div className={`pedigree-node ${genderLabel === 'Mâle' ? 'male' : 'female'}`} style={{opacity: 0.5}}>
                    <div className="pedigree-node-name">Inconnu</div>
                    <div className="pedigree-node-breed">Lignée non enregistrée</div>
                </div>
            );
        }
        return (
            <div className={`pedigree-node ${node.sexe === 'Mâle' ? 'male' : 'female'}`}>
                <div className="pedigree-node-name" title={node.nom}>{node.nom}</div>
                <div className="pedigree-node-breed">{node.race}</div>
            </div>
        );
    };

    return (
        <div className="modal-overlay" onClick={onClose}>
            <div className="modal-content glass-panel" style={{maxWidth: '850px'}} onClick={e => e.stopPropagation()}>
                <div className="modal-header">
                    <h2>Fiche d'Identité & Pedigree</h2>
                    <button className="modal-close" onClick={onClose}>&times;</button>
                </div>

                <div className="details-container">
                    {/* Left: Basic info & Photo */}
                    <div className="profile-panel">
                        <div className="profile-image-container">
                            <img 
                                src={data.image_url ? data.image_url : 'hero_fantasia.webp'} 
                                alt={data.nom} 
                                className="profile-image" 
                            />
                        </div>

                        <div className="info-grid">
                            <div className="info-box">
                                <div className="info-label">Nom</div>
                                <div className="info-value">{data.nom}</div>
                            </div>
                            <div className="info-box">
                                <div className="info-label">Race</div>
                                <div className="info-value">{data.race}</div>
                            </div>
                            <div className="info-box">
                                <div className="info-label">Sexe</div>
                                <div className="info-value">{data.sexe}</div>
                            </div>
                            <div className="info-box">
                                <div className="info-label">Robe</div>
                                <div className="info-value">{data.robe || 'Non précisé'}</div>
                            </div>
                            <div className="info-box" style={{gridColumn: 'span 2'}}>
                                <div className="info-label">Date de Naissance</div>
                                <div className="info-value">
                                    {data.date_naissance ? new Date(data.date_naissance).toLocaleDateString('fr-FR') : 'Non précisée'}
                                </div>
                            </div>
                        </div>

                        {data.owner_nom && (
                            <div className="glass-panel" style={{padding:'1rem', background:'rgba(212, 175, 55, 0.05)', borderColor:'var(--border-gold)'}}>
                                <h4 style={{color:'var(--accent-gold)', marginBottom:'0.25rem'}}>Propriétaire</h4>
                                <p><strong>{data.owner_nom}</strong></p>
                                {data.owner_tel && <p style={{fontSize:'0.85rem', color:'var(--text-muted)'}}>📞 {data.owner_tel}</p>}
                                {data.owner_email && <p style={{fontSize:'0.85rem', color:'var(--text-muted)'}}>✉️ {data.owner_email}</p>}
                            </div>
                        )}
                    </div>

                    {/* Right: Pedigree Tree */}
                    <div className="pedigree-wrapper">
                        <h3>Pedigree sur 3 Générations</h3>
                        <p style={{fontSize:'0.85rem', color:'var(--text-muted)', marginBottom:'1rem'}}>
                            L'arbre généalogique montre le père (haut) et la mère (bas) remontant aux grands-parents.
                        </p>

                        <div className="pedigree-tree">
                            {/* Column 1: Selected Horse */}
                            <div className="pedigree-col pedigree-col-1">
                                <div className={`pedigree-node ${data.sexe === 'Mâle' ? 'male' : 'female'}`} style={{borderColor: 'var(--accent-gold)'}}>
                                    <div className="pedigree-node-name" style={{color: 'var(--accent-gold)'}}>{data.nom}</div>
                                    <div className="pedigree-node-breed">{data.race}</div>
                                </div>
                            </div>

                            <div className="pedigree-connector"></div>

                            {/* Column 2: Parents */}
                            <div className="pedigree-col pedigree-col-2">
                                {/* Father (Sire) */}
                                {renderPedigreeNode(pedigree?.father, 'Mâle')}
                                {/* Mother (Dam) */}
                                {renderPedigreeNode(pedigree?.mother, 'Femelle')}
                            </div>

                            <div className="pedigree-connector"></div>

                            {/* Column 3: Grandparents */}
                            <div className="pedigree-col pedigree-col-3">
                                {/* Paternal Grandparents */}
                                {renderPedigreeNode(pedigree?.father?.father, 'Mâle')}
                                {renderPedigreeNode(pedigree?.father?.mother, 'Femelle')}
                                
                                {/* Maternal Grandparents */}
                                {renderPedigreeNode(pedigree?.mother?.father, 'Mâle')}
                                {renderPedigreeNode(pedigree?.mother?.mother, 'Femelle')}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

// Assign to window to make available globally in Babel
window.ChevalDetail = ChevalDetail;
