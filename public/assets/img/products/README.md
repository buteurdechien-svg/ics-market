# Photos réelles des produits

Ce dossier accueille les **vraies photos** des produits. Tant qu'une photo
n'est pas présente, le site affiche l'illustration ICS Market par défaut.
Dès que vous déposez un fichier au bon nom, le site l'utilise automatiquement
(aucune modification de code nécessaire).

## Comment faire

1. Ouvrez le fichier **`NOMS_DES_FICHIERS.csv`** (présent dans ce dossier).
   Il liste les 120 produits avec, pour chacun, le **nom exact** du fichier
   image à déposer (colonne `nom_du_fichier_image_a_deposer`).
2. Pour chaque produit, placez sa photo dans ce dossier **avec exactement ce
   nom**. Exemple :
   - Produit : *Samsung Galaxy A05 64 Go*
   - Fichier à déposer : `samsung-galaxy-a05-64-go-1.jpg`
3. C'est tout. Rechargez la page du site : la vraie photo remplace l'icône.

## Formats acceptés

`.jpg`, `.jpeg`, `.png`, `.webp` (le site teste ces extensions dans cet ordre).
Idéal : image carrée, ~800×800 px, fond clair, moins de 300 Ko.

## Important — droits d'usage

N'utilisez que des images dont vous détenez les droits (vos propres photos,
visuels fournis officiellement par la marque dans le cadre de vos accords, ou
banques d'images libres de droits). Ce dépôt GitHub étant **public**, les
images que vous y ajoutez y seront publiquement redistribuées : assurez-vous
que vos accords couvrent cette redistribution publique. En cas de doute,
hébergez les photos uniquement sur votre VM (hors Git) : ajoutez une ligne
`public/assets/img/products/*.jpg` dans `.gitignore`.
