<?php
// index.php - Main Entry Point and DB Seeder

require_once 'database.php';

// Initialize connection (this will run the schema.sql in SQLite if database doesn't exist)
$db = Database::getConnection();

// Check if database needs seeding
$stmt = $db->query("SELECT COUNT(*) as count FROM cheval");
$row = $stmt->fetch();
$horseCount = (int)$row['count'];

if ($horseCount === 0) {
    // Seed database with mock Tunisian horse data
    try {
        // 1. Seed Owners (Propriétaires / Haras)
        $owners = array(
            array('nom' => 'Haras National de Sidi Thabet', 'telephone' => '71 546 222', 'email' => 'contact@sidithabet.tn', 'adresse' => 'Sidi Thabet, Ariana'),
            array('nom' => 'Haras El Battan', 'telephone' => '72 654 888', 'email' => 'elbattan@defense.tn', 'adresse' => 'El Battan, Manouba'),
            array('nom' => 'Écurie Belhassen Slimane', 'telephone' => '98 333 444', 'email' => 'slimane.belhassen@gmail.com', 'adresse' => 'Soukra, Ariana')
        );
        
        $owner_ids = array();
        $stmt_owner = $db->prepare("INSERT INTO owner (nom, telephone, email, adresse) VALUES (:nom, :telephone, :email, :adresse)");
        foreach ($owners as $o) {
            $stmt_owner->execute(array(
                ':nom' => $o['nom'],
                ':telephone' => $o['telephone'],
                ':email' => $o['email'],
                ':adresse' => $o['adresse']
            ));
            $owner_ids[] = $db->lastInsertId();
        }

        // 2. Seed Jockeys
        $jockeys = array(
            array('nom' => 'Yassine El Kahlaoui', 'nationalite' => 'Tunisienne', 'experience_annees' => 8),
            array('nom' => 'Slimen Ben Ali', 'nationalite' => 'Tunisienne', 'experience_annees' => 12),
            array('nom' => 'Anis Al-Mabrouk', 'nationalite' => 'Tunisienne', 'experience_annees' => 4)
        );
        
        $jockey_ids = array();
        $stmt_jockey = $db->prepare("INSERT INTO jockey (nom, nationalite, experience_annees) VALUES (:nom, :nationalite, :experience_annees)");
        foreach ($jockeys as $j) {
            $stmt_jockey->execute(array(
                ':nom' => $j['nom'],
                ':nationalite' => $j['nationalite'],
                ':experience_annees' => $j['experience_annees']
            ));
            $jockey_ids[] = $db->lastInsertId();
        }

        // 3. Seed Horses (Chevaux) - setting up pedigree
        // Generation 3: Grandparents
        $grandparents = array(
            // Paternal Grandparents for Nassim
            array('nom' => 'Dahman', 'race' => 'Pur-Sang Arabe', 'sexe' => 'Mâle', 'date_naissance' => '2010-04-12', 'robe' => 'Gris', 'pere_id' => null, 'mere_id' => null, 'owner_id' => $owner_ids[0], 'image_url' => null),
            array('nom' => 'Halima', 'race' => 'Pur-Sang Arabe', 'sexe' => 'Femelle', 'date_naissance' => '2012-05-18', 'robe' => 'Bai', 'pere_id' => null, 'mere_id' => null, 'owner_id' => $owner_ids[0], 'image_url' => null),
            // Maternal Grandparents for Nassim
            array('nom' => 'Chafik', 'race' => 'Pur-Sang Arabe', 'sexe' => 'Mâle', 'date_naissance' => '2009-02-28', 'robe' => 'Alezan', 'pere_id' => null, 'mere_id' => null, 'owner_id' => $owner_ids[1], 'image_url' => null),
            array('nom' => 'Ouarda', 'race' => 'Pur-Sang Arabe', 'sexe' => 'Femelle', 'date_naissance' => '2011-08-05', 'robe' => 'Gris', 'pere_id' => null, 'mere_id' => null, 'owner_id' => $owner_ids[1], 'image_url' => null)
        );

        $gp_ids = array();
        $stmt_horse = $db->prepare("INSERT INTO cheval (nom, race, sexe, date_naissance, robe, pere_id, mere_id, owner_id, image_url) VALUES (:nom, :race, :sexe, :date_naissance, :robe, :pere_id, :mere_id, :owner_id, :image_url)");
        foreach ($grandparents as $gp) {
            $stmt_horse->execute(array(
                ':nom' => $gp['nom'],
                ':race' => $gp['race'],
                ':sexe' => $gp['sexe'],
                ':date_naissance' => $gp['date_naissance'],
                ':robe' => $gp['robe'],
                ':pere_id' => null,
                ':mere_id' => null,
                ':owner_id' => $gp['owner_id'],
                ':image_url' => null
            ));
            $gp_ids[] = $db->lastInsertId();
        }

        // Generation 2: Parents
        // Nassim's Father (Dahman x Halima)
        $stmt_horse->execute(array(
            ':nom' => 'Sabri', 'race' => 'Pur-Sang Arabe', 'sexe' => 'Mâle', 'date_naissance' => '2017-03-20', 'robe' => 'Gris',
            ':pere_id' => $gp_ids[0], ':mere_id' => $gp_ids[1], ':owner_id' => $owner_ids[0], ':image_url' => null
        ));
        $father_id = $db->lastInsertId();

        // Nassim's Mother (Chafik x Ouarda)
        $stmt_horse->execute(array(
            ':nom' => 'Kamila', 'race' => 'Pur-Sang Arabe', 'sexe' => 'Femelle', 'date_naissance' => '2018-06-14', 'robe' => 'Alezan',
            ':pere_id' => $gp_ids[2], ':mere_id' => $gp_ids[3], ':owner_id' => $owner_ids[1], ':image_url' => null
        ));
        $mother_id = $db->lastInsertId();

        // Generation 1: Famous Horses
        // Nassim (Sabri x Kamila) - Pur-Sang Arabe
        $stmt_horse->execute(array(
            ':nom' => 'Nassim', 'race' => 'Pur-Sang Arabe', 'sexe' => 'Mâle', 'date_naissance' => '2022-04-10', 'robe' => 'Gris',
            ':pere_id' => $father_id, ':mere_id' => $mother_id, ':owner_id' => $owner_ids[0], ':image_url' => 'pur_sang_arabe.webp'
        ));
        $nassim_id = $db->lastInsertId();

        // Chahine - Barbe Tunisien (Breeder: Haras El Battan)
        $stmt_horse->execute(array(
            ':nom' => 'Chahine', 'race' => 'Barbe', 'sexe' => 'Mâle', 'date_naissance' => '2020-05-15', 'robe' => 'Noir',
            ':pere_id' => null, ':mere_id' => null, ':owner_id' => $owner_ids[1], ':image_url' => 'barbe_tunisien.webp'
        ));
        $chahine_id = $db->lastInsertId();

        // Jasmine - Arabe-Barbe (Breeder: Private Breeder)
        $stmt_horse->execute(array(
            ':nom' => 'Jasmine', 'race' => 'Arabe-Barbe', 'sexe' => 'Femelle', 'date_naissance' => '2021-09-02', 'robe' => 'Bai',
            ':pere_id' => $nassim_id, ':mere_id' => null, ':owner_id' => $owner_ids[2], ':image_url' => 'hero_fantasia.webp'
        ));
        $jasmine_id = $db->lastInsertId();

        // 4. Seed Races (Courses)
        $courses = array(
            array('nom' => 'Grand Prix de Sidi Thabet', 'date_course' => '2026-05-12', 'lieu' => 'Hippodrome de Ksar Saïd, Tunis', 'distance' => 1600, 'prix_millimes' => 25000000), // 25,000 DT
            array('nom' => 'Prix de l\'Indépendance', 'date_course' => '2026-03-20', 'lieu' => 'Hippodrome de Ksar Saïd, Tunis', 'distance' => 2000, 'prix_millimes' => 40000000) // 40,000 DT
        );

        $course_ids = array();
        $stmt_course = $db->prepare("INSERT INTO course (nom, date_course, lieu, distance, prix_millimes) VALUES (:nom, :date_course, :lieu, :distance, :prix_millimes)");
        foreach ($courses as $c) {
            $stmt_course->execute(array(
                ':nom' => $c['nom'],
                ':date_course' => $c['date_course'],
                ':lieu' => $c['lieu'],
                ':distance' => $c['distance'],
                ':prix_millimes' => $c['prix_millimes']
            ));
            $course_ids[] = $db->lastInsertId();
        }

        // 5. Seed Participations
        $participations = array(
            // Nassim in Grand Prix de Sidi Thabet -> 1st place, ridden by Slimen
            array('cheval_id' => $nassim_id, 'course_id' => $course_ids[0], 'jockey_id' => $jockey_ids[1], 'classement' => 1),
            // Chahine in Grand Prix de Sidi Thabet -> 2nd place, ridden by Yassine
            array('cheval_id' => $chahine_id, 'course_id' => $course_ids[0], 'jockey_id' => $jockey_ids[0], 'classement' => 2),
            // Jasmine in Grand Prix de Sidi Thabet -> 3rd place, ridden by Anis
            array('cheval_id' => $jasmine_id, 'course_id' => $course_ids[0], 'jockey_id' => $jockey_ids[2], 'classement' => 3),
            
            // Chahine in Prix de l'Indépendance -> 1st place, ridden by Slimen
            array('cheval_id' => $chahine_id, 'course_id' => $course_ids[1], 'jockey_id' => $jockey_ids[1], 'classement' => 1)
        );

        $stmt_part = $db->prepare("INSERT INTO participation (cheval_id, course_id, jockey_id, classement) VALUES (:cheval_id, :course_id, :jockey_id, :classement)");
        foreach ($participations as $p) {
            $stmt_part->execute(array(
                ':cheval_id' => $p['cheval_id'],
                ':course_id' => $p['course_id'],
                ':jockey_id' => $p['jockey_id'],
                ':classement' => $p['classement']
            ));
        }

    } catch (Exception $e) {
        // Log or handle error quietly, server will still render view
    }
}

// Serve the index.html template (React Frontend)
include_once 'index.html';
