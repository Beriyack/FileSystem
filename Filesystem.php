<?php
class Filesystem {

    /**
     * Cette méthode crée un dossier à l'emplacement spécifié et crée l'arborescence si elle n'existe pas.
     * Par défaut, le chemin est dans le même dossier que la classe.
     * La méthode vérifie si le dossier existe déjà. Si c'est le cas, le dossier n'est pas créé.
     * La méthode retourne un tableau avec deux éléments : le premier est un booléen indiquant si 
     * l'opération a réussi ou échoué, et le second est un message de confirmation ou d'erreur.
     * 
     * @param string $path Le chemin absolu ou relatif du dossier à créer
     * @return array Un tableau contenant un booléen et un message de confirmation ou d'erreur
     */
    public static function CreateFolder(string $path = "./Folder"): array {
        $result = array();

        if (is_dir($path)) { // Vérifie si le dossier existe déjà
            $result[] = false;
            $result[] = "Folder already exists";
            return $result;
        } else {
            if (mkdir($path, 0777, true)) { // Crée le dossier avec l'arborescence si nécessaire
                $result[] = true;
                $result[] = "Folder created successfully";
                return $result;
            } else {
                $result[] = false;
                $result[] = "Error creating folder";
                return $result;
            }
        }
    }
    
    /**
     * Cette méthode crée un fichier à l'emplacement spécifié et crée l'arborescence si elle n'existe pas.
     * Par défaut, le chemin est dans le même dossier que la classe.
     * La méthode vérifie si le fichier existe déjà. Si c'est le cas, le fichier n'est pas créé.
     * La méthode retourne un tableau avec deux éléments : le premier est un booléen indiquant si 
     * l'opération a réussi ou échoué, et le second est un message de confirmation ou d'erreur.
     * 
     * @param string $path Le chemin absolu ou relatif du fichier à créer
     * @return array Un tableau contenant un booléen et un message de confirmation ou d'erreur
     */
    public static function CreateFile(string $path = "./file.txt"): array {
        $result = array();

        if (file_exists($path)) { // Vérifie si le fichier existe déjà
            $result[] = false;
            $result[] = "File already exists";
            return $result;
        } else {
            if (touch($path)) { // Crée le fichier avec le contenu vide
                $result[] = true;
                $result[] = "File created successfully";
                return $result;
            } else {
                $result[] = false;
                $result[] = "Error creating file";
                return $result;
            }
        }
    }
    
    /**
     * Cette méthode retourne les informations du fichier ou du dossier spécifié.
     * Par défaut, elle affiche les informations du fichier de la classe.
     * La méthode retourne un tableau avec deux éléments : le premier est un booléen indiquant si 
     * l'opération a réussi ou échoué, et le second est un tableau d'informations sur le fichier ou le dossier.
     * 
     * @param string $path Le chemin absolu ou relatif du fichier ou du dossier dont on veut les informations
     * @return array Un tableau contenant les informations du fichier ou dossier.
     */
    public static function Info(string $path = __FILE__): array {
        $fullPath = __DIR__ . '/' . $path;
        if (is_file($fullPath) || is_dir($fullPath)) {
            return stat($fullPath);
        } else {
            return "Invalid file/directory path";
        }
    }

    /**
     * Cette méthode insère du text (deuxième paramètre) dans le fichier (premier paramètre).
     * Si le troisième paramètre est à "true", le fichier est créé (w).
     * Par défaut, le chemin du fichier est dans le même dossier que la class, il contient "" et le dernier paramètre est "true".
     * La méthode retourne un tableau, dans la première case c'est true ou false et dans la deuxième est le message de confirmation ou d'erreur
     * 
     * @param string $path Le chemin du fichier dans lequel écrire.
     * @param string $content Le texte à insérer.
     * @param bool $create Si true, le fichier est recréé ou créé s'il n'existe pas.
     * @return array Un tableau contenant les informations du fichier ou dossier.
     */
    public static function Write(string $path = "./file.txt", string $content = '', bool $create = true): array
    {
        $result = array();

        // Si le fichier n'existe pas on le crée.
        if (!file_exists($path)) {
            $dir = dirname($path);
            // On crée le dossier parent si nécessaire.
            if (!file_exists($dir)) {
                if (!self::CreateFolder($dir)[0]) {
                    $result[] = false;
                    $result[] = "Une erreur est survenue lors de la création du dossier.";
                    return $result;
                }
            }
        }
        // On ouvre le fichier en mode append ou write selon la valeur de $mode.
        if ($create === true) {
            $handle = fopen($path, "w");
        } else {
            $handle = fopen($path, "a");
        }

        // On écrit le contenu dans le fichier.
        if (fwrite($handle, $content)) {
            $result[] = true;
            $result[] = "Le texte a été inséré dans le fichier avec succès.";
            fclose($handle);
        } else {
            $result[] = false;
            $result[] = "Impossible d'écrire dans le fichier.";
            fclose($handle);
        }

        return $result;
    }
    
    /**
     * Cette méthode supprime le fichier spécifié.
     * Par défaut, le chemin est du fichier est dans le même dossier que la classe.
     * 
     * @param string $path Le chemin absolu ou relatif vers le fichier à supprimer.
     * @return array Un tableau contenant un booléen et un message de confirmation ou d'erreur.
     */
    public static function DeleteFile(string $path = "./file.txt"): array {
        $result = array();
     
        if (file_exists($path)) {
            if (is_file($path)) {
                if (unlink($path)) {
                    $result[] = true;
                    $result[] = "Le fichier a été supprimé avec succès.";
                } else {
                    $result[] = false;
                    $result[] = "Une erreur est survenue lors de la suppression du fichier.";
                }
            } else {
                $result = false;
                $result = "Le chemin spécifié correspond à un dossier. Utilisez DeleteFolder() pour supprimer un dossier.";
            }
        } else {
            $result = false;
            $result = "Le fichier à supprimer n'existe pas.";
        }
        return $result;
    }
    
    /**
     * Cette méthode supprime le dossier spécifié, si le deuxième paramètre est à "true", toute l'arborescence est supprimée.
     * Par défaut, le chemin est du dossier est dans le même dossier que la classe.
     * La méthode retourne un tableau, dans la première case c'est true ou false et dans la deuxième est le message de confirmation ou d'erreur
     * 
     * @param string $path Le chemin absolu ou relatif vers le dossier à supprimer.
     * @param bool $recursive 
     * @return array Un tableau contenant un booléen et un message de confirmation ou d'erreur.
     */
    public static function DeleteFolder(string $path = "./Folder", bool $recursive = false) {
        $result = array();

        if (is_dir($path)) {
            if ($recursive) {
                $files = glob($path . '/*');
                foreach ($files as $file) {
                    if (is_dir($file)) {
                        self::DeleteFolder($file, $recursive);
                    } else {
                        unlink($file);
                    }
                }
                if (rmdir($path)) {
                    $result[] = true;
                    $result[] = "Le dossier et son contenu ont été supprimés avec succès.";
                    return $result;
                } else {
                    $result[] = false;
                    $result[] = "Une erreur est survenue lors de la suppression du dossier.";
                    return $result;
                }
            } else {
                if (rmdir($path)) {
                    $result[] = true;
                    $result[] = "Le dossier et son contenu ont été supprimés avec succès.";
                    return $result;
                } else {
                    $result[] = false;
                    $result[] = "Cannot delete non-empty directory without recursive flag";
                    return $result;
                }
            }
        } else {
            $result[] = false;
            $result[] = "Directory does not exist";
            return $result;
        }
    }
}