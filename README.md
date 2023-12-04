# Description
Script tout simple qui permet de récupérer l'identifiant pérenne d'une notice HAL sur la base d'un lien direct vers le PDF profond.

Si je dispose d'un lien profond vers un PDF sur HAL, par exemple : https://cel.archives-ouvertes.fr/docs/00/09/29/37/PDF/pelat.pdf le but de ce script est de me renvoyer le lien canonique de la notice HAL correspondante, ici : https://hal.archives-ouvertes.fr/hal-00929337

Cela ne fonctionne que lorsque le PDF contient une page de titre générée par HAL, ce qui ne semble pas toujours être le cas.

# Use case
Ce script a été créé en vue d'harmoniser les liens vers HAL que l'on trouve sur wikipédia et faire en sorte, en utilisant le lien canonique plutôt qu'un lien profond, de s'assurer de la pérennité du lien.

# données source
Il est possible d'interroger la base de données wikipédia à l'aide de la requête suivante via Quarry ([lien direct](https://quarry.wmcloud.org/query/78405)) : 
```sql
SELECT page_title, el_to_domain_index, el_to_path
FROM externallinks
JOIN page ON page_id = el_from
WHERE page_namespace = 0
AND 
(
  (el_to_domain_index LIKE "https://fr.archives-ouvertes.%")
  OR
  (el_to_domain_index LIKE "http://fr.archives-ouvertes.%")
  OR
  (el_to_domain_index LIKE "https://science.hal.%")
)
```