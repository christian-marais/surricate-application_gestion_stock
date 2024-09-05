call drop_table;
call create_tables1;
call jeu_dessai_1;

/**---------------------------PARTIE POUR LA CREATION DES TRIGGERS------------------*/
  /*1verifie les email*/
    DROP TRIGGER IF EXISTS check_mail;
    DELIMITER //
    CREATE TRIGGER check_mail
    BEFORE INSERT ON user FOR EACH ROW
    BEGIN
    IF New.email_user NOT LIKE '%@%' THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = "Le mail entré n'est pas conforme";
    END IF;
    END//
    DELIMITER ;

  /*2empeche les insertions dès que la commande a déjà recue une livraison*/
    DROP TRIGGER IF EXISTS no_insert_Com_over_livr;
    DELIMITER //
      CREATE TRIGGER no_insert_com_over_livr
      BEFORE INSERT 
      ON commande
      FOR EACH ROW
      IF New.num_com in (SELECT bordereau_reception.num_com FROM bordereau_reception) THEN
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Modification interdite. La commande a déjà recue des livraisons';
      END IF//
    DELIMITER ;

  /*3empêche les insertions si l'ensemble des livraisons est supérieure à la commande*/
    DROP TRIGGER IF EXISTS no_com_sup_livr;
    DELIMITER //
    CREATE TRIGGER no_com_sup_livr
    BEFORE INSERT ON details_reception FOR EACH ROW  
    BEGIN
    DECLARE marge integer;
    SET @marge = (SELECT t.marge FROM (SELECT DISTINCT details_commande.num_com,details_commande.id_article, details_commande.qte-t1.qteLivre as marge
        FROM details_commande
        INNER JOIN commande ON commande.num_com = details_commande
        .num_com
        INNER JOIN article ON article.id_article =  details_commande.id_article
        LEFT JOIN (SELECT details_reception.id_article as ID, sum(details_reception.qte) as "Qtelivre" 
            FROM details_reception
            GROUP BY details_reception.id_article) as t1 ON t1.ID = details_commande.id_article
        ) as t
        where t.id_article=New.id_article
        AND t.num_com in (SELECT num_com FROM bordereau_reception WHERE id_reception = New.id_reception));
    IF New.qte > @marge THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Modification interdite. La livraison ne peut être supérieure à la commande';
    END IF;
    END//
    DELIMITER ;
  
  /*4empeche les update dans  les commandes dès qu'elle a déjà recue une livraison*/
    DROP TRIGGER IF EXISTS no_upd_com_over_livr;
    DELIMITER //
    CREATE TRIGGER no_upd_com_over_livr
    BEFORE UPDATE
    ON details_commande
    FOR EACH ROW
    IF New.num_com in (SELECT bordereau_reception.num_com FROM bordereau_reception) THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Modification interdite. La commande a déjà recue des livraisons';
    END IF//
    DELIMITER ;
  /*5empeche les delete dans  les commandes dès qu'elle a déjà recue une livraison*/
    DROP TRIGGER IF EXISTS no_del_com_over_livr;
    DELIMITER //
    CREATE TRIGGER no_del_com_livr
    BEFORE DELETE
    ON details_commande
    FOR EACH ROW
    IF OLD.num_com in (SELECT bordereau_reception.num_com FROM bordereau_reception) THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Suppression interdite. La commande a déjà recue des livraisons';
    END IF//
    DELIMITER ;
  /*6empêche les insertions dans les sorties si l'ensemble est supérieure au stock disponible*/
    DROP TRIGGER IF EXISTS no_util_sup_stock;
    DELIMITER //
    CREATE TRIGGER no_util_sup_stock
    AFTER INSERT ON details_utilisation FOR EACH ROW  
    BEGIN
    DECLARE stock integer;
    SET @stock =(SELECT stock_by_article(New.id_article)) ;
    IF @stock < 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Modification interdite. Le stock ne peut pas être négatif';
    END IF;
    END//
    DELIMITER ;
  



/**---------------------------PARTIE POUR LA CREATION DES PROCEDURES---------------------------------**/

/*----------------PROCEDURE RENVOIE LE CENTRE EN COURS-------------------------*/
                    DROP PROCEDURE IF EXISTS get_centre;
                      DELIMITER //
                      CREATE PROCEDURE get_centre(IN userId INT)
                      BEGIN
                      SELECT user.id_groupe,id_centre 
                      FROM user 
                      INNER JOIN groupe
                      ON groupe.id_groupe = user.id_groupe
                      WHERE id_user = userId;
                      END//
                      DELIMITER ; 

/**---------------PROCEDURE POUR LA CREATION DES TABLES-----------**/
        START TRANSACTION;
        DROP PROCEDURE IF EXISTS create_tables1;
        DELIMITER //
        CREATE PROCEDURE create_tables1()
        BEGIN 
          /**-----------------------CREATE ALL TABLES------------------------ **/


              CREATE TABLE centre (
              id_centre varchar(50) NOT NULL,
              nom_centre varchar(255) NOT NULL,
              PRIMARY KEY (id_centre)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

            
            CREATE TABLE fournisseur (
              id_fournisseur varchar(50) NOT NULL,
              nom_fournisseur varchar(255) NOT NULL,
              adresse_fournisseur varchar(255) NOT NULL,
              cp_fournisseur varchar(255) NOT NULL,
              nom_contact varchar(255) DEFAULT NULL,
              prenom_contact varchar(255) DEFAULT NULL,
              fonction varchar(255) DEFAULT NULL,
              email varchar(255) NOT NULL,
              tel varchar(255) DEFAULT NULL,
              active boolean NOT NULL DEFAULT 1,
              PRIMARY KEY (id_fournisseur)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

            

            CREATE TABLE domaine (
              id_domaine varchar(50) NOT NULL,
              libelle_domaine varchar(255) NOT NULL,
              id_centre VARCHAR (50) NOT NULL,
              FOREIGN KEY (id_centre) REFERENCES centre(id_centre),
              PRIMARY KEY (id_domaine,id_centre)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

            

            CREATE TABLE formation (
              id_formation varchar(50) NOT NULL,
              libelle_formation varchar(255) NOT NULL,
              id_domaine varchar(50) NOT NULL,
              PRIMARY KEY (id_formation),
              FOREIGN KEY (id_domaine) REFERENCES domaine(id_domaine)
            )ENGINE=InnoDB DEFAULT CHARSET=latin1;


            
          
            CREATE TABLE groupe (
              id_groupe varchar(50) NOT NULL,
              libelle_groupe varchar(255) NOT NULL,
              id_formation varchar(50),
              id_centre varchar(50),
              FOREIGN KEY (id_formation) REFERENCES formation(id_formation),
              FOREIGN KEY (id_centre) REFERENCES centre(id_centre),
              PRIMARY KEY (id_groupe,id_centre)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

            
            CREATE TABLE cursus (
              id_cursus VARCHAR(50) NOT NULL,
              id_centre varchar(50) NOT NULL,
              id_formation varchar(50) NOT NULL,
              annee INT(5) DEFAULT NULL,
              PRIMARY KEY (id_cursus),
              FOREIGN KEY (id_formation) REFERENCES formation (id_formation),
              FOREIGN KEY (id_centre) REFERENCES centre (id_centre)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

            CREATE TABLE user (
              id_user int(11) AUTO_INCREMENT NOT NULL,
              nom_user varchar(255) NOT NULL,
              prenom_user varchar(255) NOT NULL,
              fonction_user varchar(255),
              email_user varchar(255) unique,
              password_user varbinary(255) NOT NULL,
              active BOOLEAN DEFAULT true NOT NULL,
              id_groupe varchar(50),
              id_cursus varchar(50) DEFAULT NULL,
              token VARBINARY(255) NULL,
              PRIMARY KEY (id_user),
              FOREIGN KEY (id_cursus) REFERENCES cursus(id_cursus),
              FOREIGN KEY (id_groupe) REFERENCES groupe(id_groupe)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;


           

            CREATE TABLE commande (
              num_com int(11) AUTO_INCREMENT NOT NULL,
              date_Com date NOT NULL,
              id_user int(11) DEFAULT NULL,
              id_fournisseur varchar(50) DEFAULT NULL,
              id_centre VARCHAR (50) NOT NULL,
              FOREIGN KEY (id_centre) REFERENCES centre(id_centre),
              PRIMARY KEY (num_com),
              CONSTRAINT commande_fk1 FOREIGN KEY (id_user) REFERENCES user(id_user),
              CONSTRAINT commande_fk2 FOREIGN KEY (id_fournisseur) REFERENCES fournisseur(id_fournisseur)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

           
            CREATE TABLE role (
              code_role varchar(50) NOT NULL,
              nom_role varchar(255) NOT NULL,
              permission JSON ,
              PRIMARY KEY (code_role)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

            

            CREATE TABLE gestion_role(
              code_role VARCHAR(50) NOT NULL,
              id_groupe varchar(50) NOT NULL,
              CONSTRAINT gestion_rolefk1 FOREIGN KEY (code_role) REFERENCES role (code_role),
              CONSTRAINT gestion_rolefk2 FOREIGN KEY(id_groupe) REFERENCES groupe(id_groupe),
              PRIMARY KEY (code_role,id_groupe)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

            


            CREATE TABLE famille_article (
              id_famille_article varchar(50) NOT NULL,
              libelle_famille varchar(255) NOT NULL,
              PRIMARY KEY (id_famille_article)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;


            


            CREATE TABLE article (
              id_article int(11) AUTO_INCREMENT  NOT NULL,
              lien_image VARCHAR(255),
              reference_article varchar(255) ,
              description_article varchar(255) NOT NULL,
              unite varchar(255),
              pu decimal(5,2) NOT NULL,
              CHECK (pu>=0),
              stock_de_securite int(11) DEFAULT 0,
              id_famille_article varchar(50) NOT NULL,
              id_fournisseur varchar(50),
              active BOOLEAN DEFAULT true,
              PRIMARY KEY (id_article),
              FOREIGN KEY (id_famille_article) REFERENCES famille_article(id_famille_article),
              FOREIGN KEY (id_fournisseur) REFERENCES fournisseur(id_fournisseur)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

            
            CREATE TABLE params (
              lien_logo VARCHAR (255) NULL,
              entreprise varchar(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

            

            CREATE TABLE bordereau_reception (
              id_reception  int(11) AUTO_INCREMENT NOT NULL,
              date_reception date NOT NULL,
              id_user int(11) NOT NULL,
              id_fournisseur varchar(50) NOT NULL,
              num_com int(11),
              id_centre VARCHAR (50) NOT NULL,
              FOREIGN KEY (id_centre) REFERENCES centre(id_centre),
              PRIMARY KEY  (id_reception),
              FOREIGN KEY (id_user) REFERENCES user(id_user),
              FOREIGN KEY (id_fournisseur) REFERENCES fournisseur(id_fournisseur),
              FOREIGN KEY (num_com) REFERENCES commande(num_com)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

            

            CREATE TABLE type_utilisation (
              code_utilisation varchar(50) NOT NULL,
              libelle_utilisation varchar(150) NOT NULL,
              PRIMARY KEY (code_utilisation)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

            

            CREATE TABLE utilisation (
              id_utilisation int(11) AUTO_INCREMENT NOT NULL,
              date_utilisation date NOT NULL,
              code_utilisation varchar(50) NOT NULL,
              id_user int(11) DEFAULT NULL,
              id_centre VARCHAR (50) NOT NULL,
              FOREIGN KEY (id_centre) REFERENCES centre(id_centre),
              PRIMARY KEY (id_utilisation),
              FOREIGN KEY (code_utilisation) REFERENCES type_utilisation (code_utilisation),
              FOREIGN KEY (id_user) REFERENCES user(id_user)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

           

            CREATE TABLE details_commande (
              num_com int(11) NOT NULL,
              id_article int(11) NOT NULL,
              qte int(11) NOT NULL,
              CHECK (qte>=0),
              FOREIGN KEY (num_com) REFERENCES commande (num_com),
              FOREIGN KEY (id_article) REFERENCES article (id_article),
              PRIMARY KEY (id_article,num_com)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

          

            CREATE TABLE details_reception (
              id_article int(11) NOT NULL,
              id_reception int(11) NOT NULL,
              qte int(11) NOT NULL,
              CHECK (qte>=0),
              FOREIGN KEY (id_article) REFERENCES article (id_article),
              FOREIGN KEY (id_reception) REFERENCES bordereau_reception (id_reception),
              PRIMARY KEY (id_article,id_reception)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

            

            CREATE TABLE details_utilisation (
              id_utilisation int(11) NOT NULL,
              id_article int(11) NOT NULL,
              qte int(11) NOT NULL,
              CHECK (qte>=0),
              FOREIGN KEY (id_article) REFERENCES article (id_article),
              FOREIGN KEY (id_utilisation) REFERENCES utilisation (id_utilisation),
              PRIMARY KEY (id_article,id_utilisation)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

            


            CREATE TABLE demande_article(
              id_demande int(11) NOT NULL,
              id_article int(11) NOT NULL,
              qte int(11) NOT NULL,
              CHECK (qte>=0),
              id_centre VARCHAR (50) DEFAULT NULL,
              FOREIGN KEY (id_centre) REFERENCES centre(id_centre),
              FOREIGN KEY (id_article) REFERENCES article (id_article),
              PRIMARY KEY (id_article,id_demande)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
            COMMIT;
        END //
        DELIMITER ;
        COMMIT;
/**---------------PROCEDURE QUI RETOURNE L'UTILISATEUR EN RESEIGNANT SON ROLE ------------**/
                  DROP PROCEDURE IF EXISTS getUserByRole;
                    DELIMITER //
                    CREATE PROCEDURE getUserByRole(IN role VARCHAR(50))
                    BEGIN
                    SELECT * FROM user WHERE id_groupe IN (SELECT id_groupe FROM gestion_role WHERE code_role = role);
                    END //
                    DELIMITER ;
/**---------------PROCEDURE DE CREATION DES TRIGGERS---------------------------**/ 
    
        DELIMITER //
        CREATE PROCEDURE create_trigger()
        BEGIN
          /**-------------------trigger et event-----------------------------**/
            /* verifie les email*/
            DROP TRIGGER IF EXISTS check_mail;
            DELIMITER //
            CREATE TRIGGER check_mail
            BEFORE INSERT ON user FOR EACH ROW
            BEGIN
            IF New.email_user NOT LIKE '%@%' THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = "Le mail entré n'est pas conforme";
            END IF;
            END//
            DELIMITER ; 

            /*empeche les insertions dès que la commande a déjà recue une livraison*/
              DROP TRIGGER IF EXISTS no_insert_Com_over_livr;
              DELIMITER //
                CREATE TRIGGER no_insert_com_over_livr
                BEFORE INSERT 
                ON details_commande
                FOR EACH ROW
                IF New.num_com in (SELECT bordereau_reception.num_com FROM bordereau_reception) THEN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Modification interdite. La commande a déjà recue des livraisons';
                END IF//
              DELIMITER ;
            
            /*empeche les delete des receptions dès qu'elles sont entrée*/
              DROP TRIGGER IF EXISTS no_del_livr;
              DELIMITER //
                CREATE TRIGGER no_del_livr
                BEFORE DELETE
                ON details_reception
                FOR EACH ROW
                IF OLD.id_reception in (SELECT id_reception FROM details_reception) THEN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Suppresion interdite. Une livraison entrée ne peut être supprimée. Renseigner une nouvelle erreur utilisation';
                END IF//
              DELIMITER ;

            /*empêche les insertions si l'ensemble des livraisons est supérieure à la commande*/
             DROP TRIGGER IF EXISTS no_insert_livr_sup_com2;
              DELIMITER //
              CREATE TRIGGER no_insert_livr_sup_com2
              AFTER INSERT ON details_reception FOR EACH ROW  
              BEGIN
		DECLARE numeroCom integer;
              DECLARE qteCom integer;
              DECLARE qteLiv integer;
			  SET @numeroCom = (select num_com from bordereau_reception where id_reception = New.id_reception);
              SET @qteCom = (select qte_com_from_one_recep_and_art(New.id_article,New.id_reception));
              SET @qteLiv =(select qte_recep_from_one_com_and_art(New.id_article, @numeroCom));
              IF @qteLiv > @qteCom THEN
              SIGNAL SQLSTATE '45000'
              SET MESSAGE_TEXT = 'Modification interdite. La livraison ne peut être supérieure à la commande';
              END IF;
              END//
              DELIMITER ;
            
             /*empêche les update si l'ensemble des livraisons est supérieure à la commande*/
              DROP TRIGGER IF EXISTS no_update_livr_sup_com;
              DELIMITER //
              CREATE TRIGGER no_update_livr_sup_com
              AFTER UPDATE ON details_reception FOR EACH ROW  
              BEGIN
              DECLARE qteCom integer;
              DECLARE qteLiv integer;
              SET @qteCom = (select (if(sum(details_commande.qte) is null,0,sum(details_commande.qte))) From details_commande where id_article = New.id_article and num_com = (select num_com from bordereau_reception where id_reception = New.id_reception));

              SET @qteLiv =(select (if(sum(details_reception.qte) is null,0,sum(details_reception.qte))) From details_reception where id_article = New.id_article and id_reception = New.id_reception);
              IF @qteLiv > @qteCom THEN
              SIGNAL SQLSTATE '45000'
              SET MESSAGE_TEXT = 'Modification interdite. La livraison ne peut être supérieure à la commande';END IF;
              END//
              DELIMITER ;
  
            
            /*empeche les update dans  les commandes dès qu'elle a déjà recue une livraison*/
              DROP TRIGGER IF EXISTS no_upd_com_over_livr;
              DELIMITER //
              CREATE TRIGGER no_upd_com_over_livr
              BEFORE UPDATE
              ON details_commande
              FOR EACH ROW
              IF New.num_com in (SELECT bordereau_reception.num_com FROM bordereau_reception) THEN
              SIGNAL SQLSTATE '45000'
              SET MESSAGE_TEXT = 'Modification interdite. La commande a déjà recue des livraisons';
              END IF//
              DELIMITER ;

              
      
              
            /*empeche les delete dans  les commandes dès qu'elle a déjà recue une livraison*/
              DROP TRIGGER IF EXISTS no_del_com_over_livr;
              DELIMITER //
              CREATE TRIGGER no_del_com_over_livr
              BEFORE DELETE
              ON details_commande
              FOR EACH ROW
              IF OLD.num_com in (SELECT bordereau_reception.num_com FROM bordereau_reception) THEN
              SIGNAL SQLSTATE '45000'
              SET MESSAGE_TEXT = 'Suppression interdite. La commande a déjà recue des livraisons';
              END IF//
              DELIMITER ;
      
        END//
        DELIMITER ;
/**---------------PROCEDURE DE CREATION DU JEU D'ESSAI 1 ----------------------------------------**/
        DROP PROCEDURE IF EXISTS jeu_dessai_1;
        DELIMITER //
        CREATE PROCEDURE jeu_dessai_1()
        BEGIN
          /**--------------------tables essais 1 ------------------------**/
              INSERT INTO fournisseur (id_fournisseur, nom_fournisseur, adresse_fournisseur, cp_fournisseur, nom_contact, prenom_contact, fonction, email, tel) VALUES
              ('Carref_SD', 'Carrefour Saint_Denis', '27 Avenue Mahatma Gandh Sainte Denis', '97441', 'LEBON', 'Testeur', 'Inconnue', 'test@test.fr', ' +262 262 47 95 00'),
              ('Carref_SS', 'Carrefour Sainte_Suzanne', '27 Avenue Mahatma Gandh Sainte Suzanne', '97441', 'LEBON', 'Testeur', 'Inconnue', 'test@test.fr', ' +262 262 47 95 00');
              
              INSERT INTO centre (id_centre, nom_centre) VALUES
              ('Bras-Panon', 'centre de Bras-Panon'),
              ('Lyon', 'centre de Lyon'),
              ('Saint-Andr', 'centre de Saint-André');

                
              INSERT INTO domaine (id_domaine, libelle_domaine,id_centre) VALUES
              ('EDUC', 'Éducation','Saint-Andr'),
              ('GAC', 'Gestion administrative et commerciale','Saint-Andr'),
              ('MEC', 'Mécanique','Saint-Andr'),
              ('MESO', 'Médiation socioculturelle','Saint-Andr'),
              ('REST', 'Restauration','Saint-Andr'),
              ('SAP', 'Service à la personne','Saint-Andr'),
              ('SECHY', 'Sécurité et Hygiène','Saint-Andr');


              INSERT INTO formation (id_formation, libelle_formation, id_domaine) VALUES
              ('ADVF', 'TP Assistant.e de Vie aux Familles (ADVF)', 'SAP'),
              ('AEPE', 'Accompagnement Éducatif Petite Enfance (AEPE)', 'SAP'),
              ('AMIS', 'TP Agent Médiation Informations Services (AMIS)', 'MESO'),
              ('APH', 'TP Agent Propreté et Hygiène (APH)', 'SECHY'),
              ('AR', 'Agent de Restauration (AR)', 'REST'),
              ('ARH', 'TP Assistant.e Ressources Humaines (ARH)', 'GAC'),
              ('BACPROMVA', 'BAC PRO Maintenance des Véhicules option A : Voitures particulières', 'MEC'),
              ('CAPMM', 'CAP Maintenance des Matériels', 'MEC'),
              ('CAPMVA', 'CAP Maintenance des Véhicules option A : Voitures particulières', 'MEC'),
              ('FPA', 'TP Formateur.trice Professionnel d’Adulte (FPA)', 'EDUC'),
              ('GCF', 'TP Gestionnaire Comptable et Fiscal', 'GAC'),
              ('NTC', 'TP Négociateur.trice Technico-Commercial.e (NTC)', 'GAC'),
              ('VCM', 'TP Vendeur.euse Conseil en Magasin (VCM)', 'GAC');

              INSERT INTO cursus (id_cursus, id_centre, id_formation, annee) VALUES
              (1, 'Saint-Andr', 'AEPE', '2020'),
              (2, 'Saint-Andr', 'ARH', '2010'),
              (3, 'Saint-Andr', 'NTC', '2010'),
              (4, 'Saint-Andr', 'AMIS', '2010'),
              (5, 'Saint-Andr', 'FPA','2010');

              INSERT INTO groupe(id_groupe, libelle_groupe,id_centre) VALUES 
              ('FADVF','formateur ADVF','Bras-Panon'),
              ('PEMP', 'employé','Lyon'),
              ('PINFO','personnel informatique','Saint-Andr'),
              ('ADMIN','Administrateurs','Saint-Andr');

              
              INSERT INTO user(id_user,nom_user,prenom_user,fonction_user,email_user,password_user,id_groupe) VALUES
              (1, 'MOURGAMA', 'Reine Clarisse', 'Directrice', 'test01@test.fr', AES_ENCRYPT('password','1c8dc6af3ee90234f9a26ffb09ae3c58d0047b59'), NULL),
              (2, 'GENCE', 'Jessica', 'Responsable Pédagogique', 'test1@test.fr', AES_ENCRYPT('password','1c8dc6af3ee90234f9a26ffb09ae3c58d0047b59'), NULL),
              (3, 'CHAN-KUI', 'Liam', 'Responsable technique', 'test2@test.fr', AES_ENCRYPT('password','1c8dc6af3ee90234f9a26ffb09ae3c58d0047b59'), 'ADMIN'),
              (4, 'BOUTCHAMA', 'Hervé', 'Formateur', 'test3@test.fr', AES_ENCRYPT('password','1c8dc6af3ee90234f9a26ffb09ae3c58d0047b59'), 'FADVF'),
              (5, 'LAYERMARD', 'Max', 'Formateur', 'test4@test.fr', AES_ENCRYPT('password','1c8dc6af3ee90234f9a26ffb09ae3c58d0047b59'), 'FADVF'),
              (6, 'MOURGAMA', 'Reine Clarisse', 'Directrice', 'test5@test.fr', AES_ENCRYPT('password','1c8dc6af3ee90234f9a26ffb09ae3c58d0047b59'), NULL),
              (7, 'admin', 'admin', '', 'admin@admin.fr', AES_ENCRYPT('1997','1c8dc6af3ee90234f9a26ffb09ae3c58d0047b59'), 'ADMIN'),
              (8, 'test', 'test', '', 'test@test.fr', AES_ENCRYPT('test','1c8dc6af3ee90234f9a26ffb09ae3c58d0047b59'), 'ADMIN');

              INSERT INTO commande (date_Com,id_user,id_fournisseur,id_centre) VALUES 
              ('2020/11/01',1,'Carref_SD','Lyon'),
              ('2021/12/04',2,'Carref_SS','Lyon'),
              ('2020/01/03',1,'Carref_SD','Lyon');

              INSERT INTO role (code_role,nom_role,permission) VALUES
              ('admin', 'Super_administrateur','[{"id": 1, "permissions": 8}, {"id": 2, "permissions": 8}, {"id": 3, "permissions": 8}, {"id": 4, "permissions": 8}, {"id": 5, "permissions": 8}, {"id": 6, "permissions": 8}, {"id": 7, "permissions": 8}, {"id": 8, "permissions": 8}, {"id": 9, "permissions": 8}, {"id": 10, "permissions": 8}, {"id": 11, "permissions": 8}, {"id": 12, "permissions": 8}, {"id": 13, "permissions": 8}, {"id": 14, "permissions": 8}]'),
              ('Formateur', 'Formateurs','[{"1":15},{"2":15}]'),
              ('Resp_log', 'Responsable logistique','{"1":15}'),
              ('Resp_orga', "Responsable de l\'organisme",'[{"1":15},{"2":5}]');

              INSERT INTO gestion_role(code_role, id_groupe) VALUES
              ('Resp_orga', 'PEMP'),
              ('admin', 'ADMIN'),
              ('Formateur', 'FADVF');

              INSERT INTO famille_article (id_famille_article, libelle_famille) VALUES
              ('Agro_Mat', 'matières premières alimentaire'),
              ('Info_Cons', 'Consommables nformatiques'),
              ('Info_Mat', 'Matériels informatiques'),
              ('Info_Pap', 'Papetterie');

              INSERT INTO article (reference_article, description_article, unite, pu, stock_de_securite, id_famille_article, id_fournisseur) VALUES
              ('rglkqs2012', 'ordinateur portable', 'pc', '800.00', 0, 'Info_Mat', 'Carref_SS'),
              ('kqs2012', "papiers canson d\'impression", 'paquet de 400 pages', '10.00', 5, 'Info_Cons', 'Carref_SD');

              INSERT INTO bordereau_reception(date_reception,id_user,id_fournisseur,num_com,id_centre) VALUES
              ('2021/11/03',1,'Carref_SS',1,"Lyon"),
              ('2022/01/13',1,'Carref_SS',1,"Lyon");

              INSERT INTO type_utilisation (code_utilisation, libelle_utilisation) VALUES
              ('produit_deteriore', 'fournitures détériorées,abîmées'),
              ('rajout', 'rajout en entrée de fourniture'),
              ('regul_inv Entree', 'Entrée régularisation inventaire'),
              ('regul_inv Sortie', 'Sortie régularisation inventaire'),
              ('sortie', 'utilisation de fourniture'),
              ('transfert Entree', 'transfert de marchandises'),
              ('transfert Sortie', 'transfert de marchandises');

              INSERT INTO utilisation (date_utilisation,code_utilisation,id_user,id_centre) VALUES
              ('2021/11/03','sortie',1,"Lyon"),
              ('2022/01/13','rajout',2,"Lyon");

              INSERT INTO details_commande(num_com,id_article,qte) VALUES
              (1,2,5),
              (2,1,6),
              (3,1,7);

              INSERT INTO details_reception(id_reception,id_article,qte) VALUES
              (1,2,4),
              (2,1,3);

              INSERT INTO details_utilisation(id_utilisation,id_article,qte) VALUES
              (1,2,2),
              (2,1,1);

              INSERT INTO demande_article (id_demande,id_article,qte) VALUES (1,1,10);
        END //
        DELIMITER ;
/**---------------PROCEDURE DE CREATION DE DATABASE--------------------------------**/
        DROP PROCEDURE IF EXISTS create_database;
        DELIMITER //
        CREATE PROCEDURE create_database()
        BEGIN
        DROP DATABASE IF EXISTS aati;
        CREATE DATABASE aati;
        END //
        DELIMITER ;
/**---------------PROCEDURE FLUX RECEPTION PAR MOIS ET ANNEE--------------------------------**/
                      DROP PROCEDURE IF EXISTS flux_reception;
                      DELIMITER //
                        CREATE PROCEDURE flux_reception()
                        BEGIN
                        SELECT MONTH(date_reception) AS mois, YEAR(date_reception) AS annee, SUM(qte) 
                        FROM bordereau_reception 
                        INNER JOIN details_reception 
                        ON details_reception.id_reception = bordereau_reception.id_reception 
                        GROUP BY annee, mois;
                        END //
                      DELIMITER ;
/**---------------PROCEDURE DE DROP ALL TABLES----------------------------**/
        DROP PROCEDURE IF EXISTS drop_table;
        DELIMITER //
        CREATE PROCEDURE drop_table()
        BEGIN
        START TRANSACTION;
        SET FOREIGN_KEY_CHECKS = 0;
        DROP TABLE IF EXISTS details_utilisation,params,details_reception,approvisionnement, article, bordereau_reception,commande,cursus,details_commande,domaine,famille_article,formation,fournisseur,gestion_role,groupe,role,type_utilisation,user,utilisation,centre,demande_article;
        COMMIT;
        END //
        DELIMITER ;

/**---------------PROCEDURE DE TRUNCATE ALL TABLES----------------------------**/
        DROP PROCEDURE IF EXISTS truncate_table;
        DELIMITER //
        CREATE PROCEDURE truncate_table()
        BEGIN
        START TRANSACTION;
        SET FOREIGN_KEY_CHECKS = 0;
        TRUNCATE details_utilisation; 
        TRUNCATE params;
        TRUNCATE details_reception;
        TRUNCATE article;
        TRUNCATE bordereau_reception;
        TRUNCATE commande;
        TRUNCATE cursus;
        TRUNCATE details_commande;
        TRUNCATE domaine;
        TRUNCATE famille_article;
        TRUNCATE formation;
        TRUNCATE fournisseur;
        TRUNCATE gestion_role;
        TRUNCATE groupe;
        TRUNCATE role;
        TRUNCATE type_utilisation;
        TRUNCATE user;
        TRUNCATE utilisation;
        TRUNCATE centre;
        TRUNCATE demande_article;
        COMMIT;
        END //
        DELIMITER ;
/**---------------PROCEDURE QUI RETOURNE UN MAIL EXISTANT DANS LA BDD renvoie si le mail est valide-----**/
                      DROP PROCEDURE IF EXISTS checkMails;
                      DELIMITER //
                      CREATE PROCEDURE checkMails(IN mail VARCHAR(255))
                      BEGIN
                      SELECT mail LIKE '%@%';
                      END//
                      DELIMITER ;
/*----------------PROCEDURE POUR LES STATUTS DU STOCK DES ARTICLE------de tous les articles par article--------------------------*/
                    DROP PROCEDURE IF EXISTS statut_des_articles;
                          DELIMITER //
                            CREATE PROCEDURE statut_des_articles()
                            BEGIN
                            SELECT *,(SELECT sum_sortie_by_article(id_article)) as Sortie ,(SELECT sum_entree_by_article(id_article)) as "Entree Hors Livraison", (SELECT sum_livraison_by_article(id_article)) as Livraison,(SELECT stock_by_article(id_article)) as "Stock disponible", (SELECT total_entree_by_article(id_article)) * pu AS "Montant Total", (SELECT sum_sortie_by_article(id_article)) * pu as 'Montant Sortie'  FROM article;
                            END //
                          DELIMITER ;
/*----------------PROCEDURE POUR LE pourcentage sorties entrées de l'user par centre------de tous les articles par article--------------------------*/
  DROP PROCEDURE IF EXISTS perc_entre_sur_sortie_user_par_centre;
        DELIMITER //
          CREATE PROCEDURE perc_entre_sur_sortie_user_par_centre(IN id_utilisateur INT)
          BEGIN
          SELECT IF(CAST(( SELECT (SELECT SUM(qte) FROM details_utilisation 
            INNER JOIN utilisation
            ON utilisation.id_utilisation = details_utilisation.id_utilisation
            WHERE code_utilisation IN ("produit_déterioré","regul_inv Sortie","sortie","transfert Sortie")			
            AND utilisation.id_user = 1
            AND utilisation. id_centre in( SELECT groupe.id_centre FROM user INNER JOIN groupe ON groupe.id_groupe = user.id_groupe WHERE user.id_user = id_utilisateur)
            GROUP BY id_article)/(SELECT SUM(qte) FROM details_reception INNER JOIN bordereau_reception ON details_reception.id_reception = bordereau_reception.id_reception
            WHERE bordereau_reception.id_centre in ( SELECT groupe.id_centre FROM user INNER JOIN groupe ON groupe.id_groupe = user.id_groupe WHERE user.id_user = id_utilisateur))*100)as decimal(5,0)) IS NULL,0,CAST(( SELECT (SELECT SUM(qte) FROM details_utilisation 
            INNER JOIN utilisation
            ON utilisation.id_utilisation = details_utilisation.id_utilisation
            WHERE code_utilisation IN ("produit_déterioré","regul_inv Sortie","sortie","transfert Sortie")			
            AND utilisation.id_user = id_utilisateur
            AND utilisation. id_centre in( SELECT groupe.id_centre FROM user INNER JOIN groupe ON groupe.id_groupe = user.id_groupe WHERE user.id_user = id_utilisateur)
            GROUP BY id_article)/(SELECT SUM(qte) FROM details_reception INNER JOIN bordereau_reception ON details_reception.id_reception = bordereau_reception.id_reception
            WHERE bordereau_reception.id_centre in ( SELECT groupe.id_centre FROM user INNER JOIN groupe ON groupe.id_groupe = user.id_groupe WHERE user.id_user = id_utilisateur))*100)as decimal(5,0))) 'pourcentage de user entree/sortie par centre';
	        END //
        DELIMITER ;

/*----------------PROCEDURE POUR LES STATUTS DU STOCK DES ARTICLE--------pour un seul article------------------------*/
                        DROP PROCEDURE IF EXISTS statut_des_articles_par_id;
                      DELIMITER //
                        CREATE PROCEDURE statut_des_articles_par_id(IN id INT)
                        BEGIN
                        SELECT *,IF((SELECT sum_sortie_by_article(id_article)) IS NULL,0,(SELECT sum_sortie_by_article(id_article)))  as Sortie ,IF((SELECT total_entree_by_article(id_article)) IS NULL,0, (SELECT total_entree_by_article(id_article))) as Entree, IF((SELECT total_entree_by_article(id_article)) - (SELECT sum_sortie_by_article(id_article)) IS NULL,0,(SELECT total_entree_by_article(id_article)) - (SELECT sum_sortie_by_article(id_article))) as "Stock disponible", IF((SELECT total_entree_by_article(id_article)) * pu IS NULL,0, (SELECT total_entree_by_article(id_article)) * pu) AS "Montant Total", IF((SELECT sum_sortie_by_article(id_article)) * pu IS NULL,0,(SELECT sum_sortie_by_article(id_article))) as 'Montant Sortie'  FROM article
                        WHERE id_article LIKE CONCAT('%',id,'%');
                        END //
                      DELIMITER ;
/*----------------PROCEDURE UTILISATION PAR MOIS PAR USER  en liste de produits-------------------------*/
        DROP PROCEDURE IF EXISTS flux_utilisation_month_user;
        DELIMITER //
          CREATE PROCEDURE flux_utilisation_month_user(In utilisateur int)
          BEGIN
         SELECT * FROM details_utilisation INNER JOIN utilisation ON details_utilisation.id_utilisation = utilisation.id_utilisation
		INNER JOIN article ON article.id_article = details_utilisation.id_article
                    WHERE utilisation.id_user = utilisateur
                    AND MONTH(utilisation.date_utilisation) = MONTH(now());
          END //
        DELIMITER ;

/*----------------PROCEDURE FLUX RECEPTION PAR MOIS ET ANNEE-------------------------*/
        DROP PROCEDURE IF EXISTS flux_reception;
        DELIMITER //
          CREATE PROCEDURE flux_reception()
          BEGIN
          SELECT MONTH(date_reception) AS mois, YEAR(date_reception) AS annee, SUM(qte) 
          FROM bordereau_reception 
          INNER JOIN details_reception 
          ON details_reception.id_reception = bordereau_reception.id_reception 
          GROUP BY annee, mois;
          END //
        DELIMITER ;
/*----------------PROCEDURE FLUX RECEPTION PAR MOIS ANNE EN COURS--------------------------------*/
        DROP PROCEDURE IF EXISTS flux_reception_current_year;
        DELIMITER //
          CREATE PROCEDURE flux_reception_current_year()
          BEGIN
          SELECT MONTH(date_reception) AS mois, SUM(qte) 
	  FROM bordereau_reception 
	  INNER JOIN details_reception ON details_reception.id_reception = bordereau_reception.id_reception WHERE YEAR(date_reception) = YEAR(now()) GROUP BY mois;
          END //
        DELIMITER ;

/*----------------PROCEDURE FLUX COMMANDE PAR MOIS ANNE EN COURS--------------------------------*/
        DROP PROCEDURE IF EXISTS flux_commande_current_year;
        DELIMITER //
          CREATE PROCEDURE flux_commande_current_year()
          BEGIN
          SELECT MONTH(date_Com) AS mois, SUM(qte) 
	  FROM commande 
	  INNER JOIN details_commande ON details_commande.num_com = commande.num_com WHERE YEAR(date_Com) = YEAR(now()) GROUP BY mois;
          END //
        DELIMITER ;

/*----------------PROCEDURE FLUX COMMANDE PAR ANNEE EN COURS POUR UN UTILISATEUR--------------------------------*/
        DROP PROCEDURE IF EXISTS flux_commande_current_year_by_user;
        DELIMITER //
          CREATE PROCEDURE flux_commande_current_year_by_user(IN utilisateur_id INT)
          BEGIN
          SELECT MONTH(date_Com) AS mois, SUM(qte) 
	  FROM commande 
	  INNER JOIN details_commande ON details_commande.num_com = commande.num_com WHERE YEAR(date_Com) = YEAR(now()) AND commande.id_user = utilisateur_id
	  GROUP BY mois;
          END //
        DELIMITER ;
	
/*----------------PROCEDURE FLUX COMMANDE MOIS EN COURS POUR UN UTILISATEUR--------------------------------*/
        DROP PROCEDURE IF EXISTS flux_commande_current_month_by_user;
        DELIMITER //
          CREATE PROCEDURE flux_commande_current_month_by_user(IN utilisateur_id INT)
          BEGIN
          SELECT MONTH(date_Com) AS mois, SUM(qte) 
	  FROM commande 
	  INNER JOIN details_commande ON details_commande.num_com = commande.num_com WHERE MONTH(date_Com) = MONTH(now()) AND commande.id_user = utilisateur_id
	  GROUP BY mois;
          END //
        DELIMITER ;
/*----------------PROCEDURE FLUX SORTIE MOIS EN COURS POUR UN UTILISATEUR--------------------------------*/
        DROP PROCEDURE IF EXISTS flux_sortie_current_month_by_user;
        DELIMITER //
          CREATE PROCEDURE flux_sortie_current_month_by_user(IN utilisateur_id INT)
          BEGIN
        SELECT *,pu*t1.quantite as montant FROM (SELECT details_utilisation.id_article,SUM(qte) as quantite
 		FROM details_utilisation
 		INNER JOIN utilisation ON utilisation.id_utilisation =details_utilisation.id_utilisation
 		WHERE utilisation.id_user = utilisateur_id
 		AND MONTH(utilisation.date_utilisation) = MONTH(now())
 		AND utilisation.code_utilisation = 'sortie'
 		GROUP BY details_utilisation.id_article) as t1
 		INNER JOIN article ON article.id_article = t1.id_article;
          END //
        DELIMITER ;

/*----------------PROCEDURE FLUX COMMANDE PAR ANNEE EN COURS POUR CHAQUE ARTICLE POUR UN UTILISATEUR--------------------------------*/
        DROP PROCEDURE IF EXISTS flux_commande_current_year_by_user_by_article;
        DELIMITER //
          CREATE PROCEDURE flux_commande_current_year_by_user_by_article(IN utilisateur_id INT)
          BEGIN
          	 SELECT *,pu*t1.quantite as montant FROM (SELECT details_commande.id_article,SUM(qte) as quantite
 			FROM details_commande
 			INNER JOIN commande ON commande.num_com =details_commande.num_com
			 WHERE commande.id_user = utilisateur_id
 			AND YEAR(commande.date_Com) = YEAR(now())
 			GROUP BY details_commande.id_article) as t1
			 INNER JOIN article ON article.id_article = t1.id_article;
          END //
        DELIMITER ;
	
/*----------------PROCEDURE FLUX COMMANDE PAR MOIS EN COURS POUR CHAQUE ARTICLE POUR UN UTILISATEUR--------------------------------*/
        DROP PROCEDURE IF EXISTS flux_commande_current_month_by_user_by_article;
        DELIMITER //
          CREATE PROCEDURE flux_commande_current_month_by_user_by_article(IN utilisateur_id INT)
          BEGIN
          	 SELECT *,pu*t1.quantite as montant FROM (SELECT details_commande.id_article,SUM(qte) as quantite
 			FROM details_commande
 			INNER JOIN commande ON commande.num_com =details_commande.num_com
			 WHERE commande.id_user = utilisateur_id
 			AND MONTH(commande.date_Com) = MONTH(now())
 			GROUP BY details_commande.id_article) as t1
			 INNER JOIN article ON article.id_article = t1.id_article;
          END //
        DELIMITER ;


/*----------------PROCEDURE FLUX COMMANDE PAR CENTRE ENTRE DEUX DATES---------centre dateb et fin-----------------------*/
         DROP PROCEDURE IF EXISTS flux_commande_par_centre_date;
         DELIMITER //
          CREATE PROCEDURE flux_commande_par_centre_date(IN centre VARCHAR(255), IN dataDeb date, IN dateFin date )
          BEGIN
          SELECT * FROM details_commande 
	INNER JOIN commande 
	ON details_commande.num_com = commande.num_com
	INNER JOIN user
	ON user.id_user = commande.id_user
	INNER JOIN groupe
	ON groupe.id_groupe = user.id_groupe
	WHERE id_centre = centre
	AND commande.date_Com BETWEEN dateDeb AND dateFin;
          END //
        DELIMITER ;
/*----------------PROCEDURE FLUX RECEPTION PAR CENTRE ENTRE DEUX DATES------------centre datedeb et fin--------------------*/
         DROP PROCEDURE IF EXISTS flux_reception_par_centre_date;
         DELIMITER //
          CREATE PROCEDURE flux_reception_par_centre_date(IN centre VARCHAR(255), IN dataDeb date, IN dateFin date )
          BEGIN
          SELECT * FROM details_reception 
          INNER JOIN reception 
          ON details_reception.id_reception = bordereau_reception.id_reception
          INNER JOIN user
          ON user.id_user = bordereau_reception.id_user
          INNER JOIN groupe
          ON groupe.id_groupe = user.id_groupe
          WHERE id_centre = centre
          AND utilisation.date_utilisation BETWEEN dateDeb AND dateFin;
          END //
        DELIMITER ;
/*----------------PROCEDURE FLUX UTILISATION PAR CENTRE ENTRE DEUX DATES-------------centre-datedeb et fin------------------*/
         DROP PROCEDURE IF EXISTS flux_utilisation_par_centre_date;
         DELIMITER //
          CREATE PROCEDURE flux_utilisation_par_centre_date(IN centre VARCHAR(255), IN dataDeb date, IN dateFin date )
          BEGIN
          SELECT * FROM details_utilisation 
          INNER JOIN utilisation 
          ON details_utilisation.id_utilisation = utilisation.id_utilisation
          INNER JOIN user
          ON user.id_user = utilisation.id_user
          INNER JOIN groupe
          ON groupe.id_groupe = user.id_groupe
          WHERE utilisation.id_centre = centre
          AND utilisation.date_utilisation BETWEEN @dateDeb AND @dateFin;
          END //
        DELIMITER ;
/*----------------PROCEDURE FLUX UTILISATION PAR CENTRE ENTRE DEUX DATES avec type utilisation--------------------------------*/
         DROP PROCEDURE IF EXISTS flux_utilisation_par_centre_date_par_mouvement;
         DELIMITER //
          CREATE PROCEDURE flux_utilisation_par_centre_date_par_mouvement(IN centre VARCHAR(255), IN mouvement varchar(255),IN dataDeb date, IN dateFin date )
          BEGIN
      	SELECT * FROM details_utilisation 
        INNER JOIN utilisation 
        ON details_utilisation.id_utilisation = utilisation.id_utilisation
        INNER JOIN user
        ON user.id_user = utilisation.id_user
        INNER JOIN groupe
        ON groupe.id_groupe = user.id_groupe
        WHERE id_centre = centre
        AND utilisation.date_utilisation BETWEEN dateDeb AND dateFin
        AND code_utilisation = mouvement;
        END //
        DELIMITER ;
/*----------------PROCEDURE FLUX UTILISATION UTILISATEUR par centre ENTRE DEUX DATES --------------------------------*/
         DROP PROCEDURE IF EXISTS flux_utilisation_par_centre_date_par_utilisateur;
         DELIMITER //
          CREATE PROCEDURE flux_utilisation_par_centre_date_par_utilisateur(IN usercentre INT,centre VARCHAR(255), IN mouvement varchar(255),IN dataDeb date, IN dateFin date )
          BEGIN
          SELECT * FROM details_utilisation 
          INNER JOIN utilisation 
          ON details_utilisation.id_utilisation = utilisation.id_utilisation
          INNER JOIN user
          ON user.id_user = utilisation.id_user
          INNER JOIN groupe
          ON groupe.id_groupe = user.id_groupe
          WHERE id_centre = centre
          AND commande.date_Com BETWEEN dateDeb AND dateFin
          AND utilisation.id_user = usercentre;
          END //
        DELIMITER ;

/*--------------------------------------------------------------------------------------*/
/**--------------------------PARTIE FUNCTIONS-----------------------------------*/
/*----------------FONCTION QUANTITE RECEPTIONNE PAR ARTICLE ET COMMANDE--------------------------------*/
   DROP FUNCTION IF EXISTS qte_recep_from_one_com_and_art;
        DELIMITER //
        CREATE FUNCTION qte_recep_from_one_com_and_art(numero_article INT,numero_commande INT)
        RETURNS INT
        BEGIN
        DECLARE quantite INT;
        SET quantite = (Select sum(qte) 
                        FROM details_reception
                        INNER JOIN bordereau_reception ON bordereau_reception.id_reception = details_reception.id_reception 
                        WHERE num_com = numero_commande 
                        AND id_article = numero_article);
        SET quantite = IF(quantite IS NULL,0,quantite);
        RETURN quantite;
        END //
        DELIMITER ;
/*----------------FONCTION QUANTITE COMMANDEE PAR ARTICLE ET COMMANDE--------------------------------*/
   DROP FUNCTION IF EXISTS qte_com_from_one_recep_and_art;
        DELIMITER //
        CREATE FUNCTION qte_com_from_one_recep_and_art(numero_article INT,numero_reception INT)
        RETURNS INT
        BEGIN
        DECLARE quantite INT;
        SET quantite = (Select qte 
                          FROM details_commande
                          WHERE details_commande.num_com = (SELECT bordereau_reception.num_com FROM bordereau_reception WHERE id_reception = numero_reception )
                          AND details_commande.id_article = numero_article);
        SET quantite = IF(quantite IS NULL,0,quantite);
        RETURN quantite;
        END //
        DELIMITER ;
        
/*----------------FONCTION POUR LES SORTIES PAR ARTICLE--------------------------------*/
   DROP FUNCTION IF EXISTS sum_sortie_by_article;
        DELIMITER //
        CREATE FUNCTION sum_sortie_by_article(numero_article INT)
        RETURNS INT
        BEGIN
        DECLARE quantite INT;
        SET quantite = (SELECT SUM(qte) FROM details_utilisation 
                    INNER JOIN utilisation
                    ON utilisation.id_utilisation = details_utilisation.id_utilisation
                    WHERE code_utilisation IN ("produit_déterioré","regul_inv Sortie","sortie","transfert Sortie")
                    AND id_article = numero_article
                    GROUP BY id_article);
        SET quantite = IF(quantite IS NULL,0,quantite);
        RETURN quantite;
        END //
        DELIMITER ;
/*----------------FONCTION POUR LE TOTAL DES LIVRAISONS PAR ARTICLE  manipulee PAR ARTICLE---------------attention ne tient pas compte des livraisons justes des flux c'est pas un stock------------------*/
       DROP FUNCTION IF EXISTS sum_livraison_by_article;
        DELIMITER //
        CREATE FUNCTION sum_livraison_by_article(numero_article INT )
        RETURNS INT
        BEGIN
        DECLARE quantite INT;
        SET quantite = (SELECT SUM(qte) FROM details_reception where id_article = numero_article);
        SET quantite = IF(quantite IS NULL,0,quantite);
        RETURN quantite;
        END //
        DELIMITER ;
/*----------------FONCTION POUR FLUX LES ENTREES  manipulee PAR ARTICLE---------------attention ne tient pas compte des livraisons justes des flux c'est pas un stock------------------*/
       DROP FUNCTION IF EXISTS sum_entree_by_article;
        DELIMITER //
        CREATE FUNCTION sum_entree_by_article(numero_article INT)
        RETURNS INT
        BEGIN
        DECLARE quantite INT;
        SET quantite = (SELECT SUM(qte) FROM details_utilisation 
                    INNER JOIN utilisation
                    ON utilisation.id_utilisation = details_utilisation.id_utilisation
                    WHERE code_utilisation IN ("rajout","regul_inv Entree","transfert Entree")
                    AND id_article = numero_article
                    GROUP BY id_article);
        SET quantite = IF(quantite IS NULL,0,quantite);
        RETURN quantite;
        END //
        DELIMITER ;
/*----------------FONCTION POUR LE TOTAL DES ENTREES PAR ARTICLE ---------------stock------------------*/
  
     DROP FUNCTION IF EXISTS total_entree_by_article;
        DELIMITER //
        CREATE FUNCTION  total_entree_by_article(numero_article INT)
        RETURNS INT
        BEGIN
        DECLARE quantite INT;
        SET quantite =(SELECT sum_livraison_by_article(numero_article)+sum_entree_By_article(numero_article) 		 as Total);
        SET quantite = IF(quantite IS NULL,0,quantite);
        RETURN quantite;
        END //
        DELIMITER ;

/*----------------FONCTION POUR LES STOCK PAR ARTICLE PAR ARTICLE---------------stock------------------*/
        DROP FUNCTION IF EXISTS stock_by_article;
        DELIMITER //
        CREATE FUNCTION stock_by_article(numero_article INT)
        RETURNS INT
        BEGIN
        DECLARE quantite INT;
        SET quantite =(SELECT total_entree_by_article(numero_article)-sum_sortie_by_article(numero_article) as Stock);
        SET quantite = IF(quantite IS NULL,0,quantite);
        RETURN quantite;
        END //
        DELIMITER ;


/*----------------FONCTION POUR LES FLUX DE MANIPULATION PAR ARTICLE----------attention ne tient pas compte des livraisons justes des flux c'est pas un stock----------------------*/
     DROP FUNCTION IF EXISTS flux_by_article;
        DELIMITER //
        CREATE FUNCTION flux_by_article(numero_article INT)
        RETURNS INT
        BEGIN
        DECLARE quantite INT;
        SET quantite = (SELECT IF((SELECT sum_entree_by_article(numero_article)) - (SELECT sum_sortie_by_article(numero_article))
       IS NULL,
       0,
       (SELECT sum_entree_by_article(numero_article)) - (SELECT sum_sortie_by_article(numero_article))) as Stock);
        RETURN quantite;
        END //
        DELIMITER ;
/**---------------FONCTION QUI RETOURNE LES USER POSSEDANT TEL ROL si les utilisateurs par groupe est valide-----**/
                    DROP FUNCTION IF EXISTS getUserByRole;
                      DELIMITER //
                      CREATE FUNCTION getUserByRole(role VARCHAR(50))
                      RETURNS JSON
                      BEGIN
                      DECLARE user JSON;
                      SET user = (SELECT GROUP_CONCAT('{',email_user,'}') 
                                FROM user 
                                WHERE id_groupe 
                                IN (SELECT id_groupe 
                                    FROM gestion_role 
                                    WHERE code_role = role));
                      RETURN user;
                      END//
                      DELIMITER ;
/*----------------FONCTION QUI RENVOI UN MAIL si le mail est valide-----**/
  DROP FUNCTION IF EXISTS check_mail_f;
  DELIMITER //
  CREATE FUNCTION check_mail_f(mail VARCHAR(255))
  RETURNS INT
  BEGIN
  DECLARE numberOfRow INT;
  SET numberOfRow = (SELECT mail LIKE '%@%');
  RETURN numberOfRow;
  END//
  DELIMITER ;



/**---------------------------------PROCEDURES ET FONCTIONS STOCK-----------------------------**/
  
/*----------------FONCTION POUR LES STOCK PAR ARTICLE PAR CENTRE  ARTICLE---------------stock------------------*/
          DROP FUNCTION IF EXISTS stock_by_article_by_centre;
          DELIMITER //
          CREATE FUNCTION stock_by_article_by_centre(numero_article INT,nom_centre VARCHAR(50))
          RETURNS INT
          BEGIN
          DECLARE quantite INT;
          SET quantite =(SELECT total_entree_by_article_by_centre(numero_article,nom_centre)-sum_sortie_by_article_by_centre(numero_article,nom_centre) as Stock);
          SET quantite = IF(quantite IS NULL,0,quantite);
          RETURN quantite;
          END //
          DELIMITER ;

/*----------------FONCTION POUR LE TOTAL DES ENTREES PAR ARTICLE PAR CENTRE ---------------stock------------------*/
    
      DROP FUNCTION IF EXISTS total_entree_by_article_by_centre;
          DELIMITER //
          CREATE FUNCTION  total_entree_by_article_by_centre(numero_article INT,nom_centre varchar(50))
          RETURNS INT
          BEGIN
          DECLARE quantite INT;
          SET quantite =(SELECT sum_livraison_by_article_by_centre(numero_article,nom_centre)+sum_entree_by_article_by_centre(numero_article,nom_centre)as Total);
          SET quantite = IF(quantite IS NULL,0,quantite);
          RETURN quantite;
          END //
          DELIMITER ;

/*----------------FONCTION POUR LE TOTAL DES LIVRAISONS PAR ARTICLE ET PAR CENTRE  manipulee PAR ARTICLE---------------attention ne tient pas compte des livraisons justes des flux c'est pas un stock------------------*/
        DROP FUNCTION IF EXISTS sum_livraison_by_article_by_centre;
          DELIMITER //
          CREATE FUNCTION sum_livraison_by_article_by_centre(numero_article INT,nom_centre VARCHAR(255))
          RETURNS INT
          BEGIN
          DECLARE quantite INT;
          SET quantite = (SELECT SUM(qte) FROM details_reception INNER JOIN bordereau_reception ON bordereau_reception.id_reception = details_reception.id_reception where id_article = numero_article AND id_centre = nom_centre);
          SET quantite = IF(quantite IS NULL,0,quantite);
          RETURN quantite;
          END //
          DELIMITER ;

/*----------------FONCTION POUR LES SORTIES PAR ARTICLE PAR CENTRE --------------------------------*/
    DROP FUNCTION IF EXISTS sum_sortie_by_article_by_centre;
          DELIMITER //
          CREATE FUNCTION sum_sortie_by_article_by_centre(numero_article INT,nom_centre VARCHAR(50))
          RETURNS INT
          BEGIN
          DECLARE quantite INT;
          SET quantite = (SELECT SUM(qte) FROM details_utilisation 
                      INNER JOIN utilisation
                      ON utilisation.id_utilisation = details_utilisation.id_utilisation
                      WHERE code_utilisation IN ("produit_deteriore","regul_inv Sortie","sortie","transfert Sortie")
                      AND id_article = numero_article
          AND id_centre = nom_centre
                      GROUP BY id_article);
          SET quantite = IF(quantite IS NULL,0,quantite);
          RETURN quantite;
          END //
          DELIMITER ;

/*----------------FONCTION POUR FLUX LES ENTREES  manipulee PAR ARTICLE PAR CENTRE---------------attention ne tient pas compte des livraisons justes des flux c'est pas un stock------------------*/
                      DROP FUNCTION IF EXISTS sum_entree_by_article_by_centre;
                        DELIMITER //
                        CREATE FUNCTION sum_entree_by_article_by_centre(numero_article INT, nom_centre VARCHAR(50))
                        RETURNS INT
                        BEGIN
                        DECLARE quantite INT;
                        SET quantite = (SELECT SUM(qte) FROM details_utilisation 
                                    INNER JOIN utilisation
                                    ON utilisation.id_utilisation = details_utilisation.id_utilisation
                                    WHERE code_utilisation IN ("rajout","regul_inv Entree","transfert Entree")
                                    AND id_article = numero_article
                        AND id_centre = nom_centre
                                    GROUP BY id_article);
                        SET quantite = IF(quantite IS NULL,0,quantite);
                        RETURN quantite;
                        END //
                        DELIMITER ;

/*----------------PROCEDURE ARTICLE MENSUEL PREFERE PAR USER  PAR USER  en liste de produits-------------------------*/
        DROP PROCEDURE IF EXISTS prefered_article_month_user;
        DELIMITER //
          CREATE PROCEDURE prefered_article_month_user(In utilisateur int)
          BEGIN
         SELECT*FROM (SELECT T.mesarticles,T.qt FROM (SELECT id_article AS mesarticles, SUM(qte) as qt FROM details_utilisation
               INNER JOIN utilisation ON details_utilisation.id_utilisation = utilisation.id_utilisation
		WHERE utilisation.id_user = utilisateur
		AND utilisation.code_utilisation ='sortie'
		AND MONTH(utilisation.date_utilisation) = MONTH(NOW())
    group by id_article) as T,(SELECT MAX(t1.qt) as max FROM (SELECT id_article, SUM(qte) as qt FROM details_utilisation
    INNER JOIN utilisation ON details_utilisation.id_utilisation = utilisation.id_utilisation
		WHERE utilisation.id_user = utilisateur
		AND MONTH(utilisation.date_utilisation) = MONTH(NOW())
		AND utilisation.code_utilisation ='sortie'
		group by id_article) AS t1) AS t2
		WHERE T.qt = t2.max) as m
		INNER JOIN article ON article.id_article = mesarticles;
          END //
        DELIMITER ;

/**---------------CREATE USER AND GRANT PRIVILEGES--------------------------**/
  CREATE USER 'aati_user'@'%' 
  IDENTIFIED WITH mysql_native_password AS 'aatipassword';
  GRANT ALL PRIVILEGES ON *.* 
  TO 'aati_user'@'%' 
  REQUIRE NONE 
  WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;
  GRANT ALL PRIVILEGES 
  ON `aati`.* 
  TO 'aati_user'@'%';