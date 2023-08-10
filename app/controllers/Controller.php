<?php
namespace app\controllers;

abstract class Controller{


    protected $viewPath;
    protected $template;

    /**
     * Afficher une vue
     *
     * @param string $fichier
     * @return void
     */
    public function render(string $view){

        // On démarre le buffer de sortie
        ob_start();

        // On génère la vue
        require($this->viewPath . strtolower($view) . '.php');
        
        // On stocke le contenu dans $content
        $content = ob_get_clean();
        
        // On fabrique le "template"
        require($this->viewPath . 'templates/' . strtolower($this->view) . '.php');
    }

    /**
     * Permet de charger un modèle
     *
     * @param string $model
     * @return void
     */
    public function loadModel(string $model){
        // On va chercher le fichier correspondant au modèle souhaité
        require_once(ROOT.'models/'.$model.'.php');
        
        // On crée une instance de ce modèle. Ainsi "Article" sera accessible par $this->Article
        $this->$model = new $model();
    }
}