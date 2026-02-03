<?php include '../includes/header.php'; ?>

    <!-- SECTION HERO CONTACT -->
    <section class="contact-hero">
        <h1 class="contact-title">Contactez-nous</h1>
        <p class="contact-subtitle">Une question ? Un projet ? Nous sommes là pour vous accompagner</p>
    </section>

    <!-- SECTION CONTENU CONTACT -->
    <section class="contact-content">
        <div class="container3">
            <div class="contact-layout">
                
                <!-- FORMULAIRE GAUCHE -->
                <div class="contact-form-container">
                    <h2>Envoyez-nous un message</h2>
                    
                    <form class="contact-form" method="POST" action="">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom">Nom *</label>
                                <input type="text" id="nom" name="nom" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="prenom">Prénom *</label>
                                <input type="text" id="prenom" name="prenom" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="telephone">Téléphone</label>
                                <input type="tel" id="telephone" name="telephone">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="sujet">Sujet *</label>
                            <input type="text" id="sujet" name="sujet" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" rows="6" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn-submit">Envoyer le message</button>
                    </form>
                </div>

                <!-- INFORMATIONS DROITE -->
                <div class="contact-info-container">
                    <h2>Nos coordonnées</h2>
                    
                    <div class="info-block">
                        <h3>📍 Adresse</h3>
                        <p>123 Rue de la Gastronomie</p>
                        <p>33000 Bordeaux, France</p>
                    </div>
                    
                    <div class="info-block">
                        <h3>📞 Téléphone</h3>
                        <p>05 56 XX XX XX</p>
                    </div>
                    
                    <div class="info-block">
                        <h3>📧 Email</h3>
                        <p>contact@vite-gourmand.fr</p>
                    </div>
                    
                    <div class="info-block">
                        <h3>🕐 Horaires d'ouverture</h3>
                        <p><strong>Lundi - Vendredi :</strong> 9h - 18h</p>
                        <p><strong>Samedi :</strong> 9h - 12h</p>
                        <p><strong>Dimanche :</strong> Sur rendez-vous</p>
                    </div>
                    
                    <div class="info-block">
                        <h3>⏰ Délai de réponse</h3>
                        <p>Nous vous répondons sous 24h ouvrées</p>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

<?php include '../includes/footer.php'; ?>