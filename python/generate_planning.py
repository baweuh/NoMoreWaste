import pandas as pd
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker
import config
import os

# Configurer la connexion à la base de données
DATABASE_URI = config.DATABASE_URI
engine = create_engine(DATABASE_URI)
Session = sessionmaker(bind=engine)
session = Session()

try:
    # Requête SQL pour extraire les données avec les détails requis
    query = """
    SELECT 
    t.delivery_id,
    t.delivery_date,
    t.start_time,
    t.end_time,
    t.pdf_report_path,
    b.name AS volunteer_name,
    s.name AS service_name
    FROM 
        Tournees t
    JOIN 
        Tournees_benevoles tb ON tb.delivery_id = t.delivery_id
    JOIN 
        Benevoles_Services bs ON tb.service_id = bs.service_id
    JOIN 
        Benevoles b ON bs.volunteer_id = b.volunteer_id
    JOIN 
        Services s ON bs.service_id = s.service_id
    ORDER BY 
        t.delivery_date, b.name;
    """

    # Exécuter la requête et créer un DataFrame
    df = pd.read_sql_query(query, engine)
    
    # Afficher les premières lignes du DataFrame pour vérifier les données
    print("DataFrame créé avec les données suivantes :")
    print(df.head())
    
    if df.empty:
        print("Le DataFrame est vide. Aucune donnée n'a été récupérée.")
    else:
        print(f"Nombre de lignes dans le DataFrame : {len(df)}")
    
    # Formater la colonne delivery_date
    if 'delivery_date' in df.columns:
        df['delivery_date'] = pd.to_datetime(df['delivery_date']).dt.strftime('%Y-%m-%d')
    
    # Créer le fichier Excel
    output_file = os.path.join('..', '..', 'uploads', 'plannings.xlsx')  # Enregistre le fichier dans le dossier uploads
    
    # Vérifiez si le dossier existe
    if not os.path.exists(os.path.dirname(output_file)):
        os.makedirs(os.path.dirname(output_file))
    
    df.to_excel(output_file, index=False)
    print(f"Fichier Excel créé avec succès à l'emplacement : {output_file}")

except Exception as e:
    print(f"Erreur : {e}")
