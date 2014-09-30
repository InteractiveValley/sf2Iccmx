<?php

namespace Richpolis\PublicacionesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Richpolis\PublicacionesBundle\Entity\Publicacion;
use Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion;

/**
 * PublicacionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PublicacionRepository extends EntityRepository
{
    public function getMaxPosicion(){
        $em=$this->getEntityManager();
       
        $query=$em->createQuery('
            SELECT MAX(c.position) as value 
            FROM PublicacionesBundle:Publicacion c 
            ORDER BY c.position ASC
        ');
        
        $max=$query->getResult();
        return $max[0]['value'];
    }

    public function getPublicacionConGaleriaPorId($publicacion_id,$active=true){
        $em=$this->getEntityManager();
        $query=$em->createQuery('
               SELECT c,p,g 
               FROM PublicacionesBundle:Publicacion p 
               JOIN c.publicaciones p 
               JOIN p.galerias c 
               WHERE c.id = :publicacion 
               AND g.isActive = :active 
               ORDER BY p.position, g.position ASC
        ')->setParameters(array('publicacion'=> $publicacion_id,'active'=>$active));
        
        $publicacions=$query->getResult();
        if(isset($publicacions[0])){
            return $publicacions[0];
        }else{
            return null;
        }
    }
    
    public function findActivos(){
        $em=$this->getEntityManager();
        $query=$em->createQuery('
               SELECT p,c,g 
               FROM PublicacionesBundle:Publicacion p 
               JOIN p.galerias g 
               JOIN p.categoria c 
               WHERE p.isActive = :publicacion 
               AND g.isActive = :galeria 
               ORDER BY p.empezo ASC, p.termino DESC, g.position ASC
        ')->setParameters(array('publicacion'=> true,'galeria'=>true));
        
        return $query->getResult();
    }
    
    public function findForSlugConObjetos($slug){
        $em=$this->getEntityManager();
        $query=$em->createQuery('
               SELECT p,c,u,g 
               FROM PublicacionesBundle:Publicacion p 
               JOIN p.galerias g 
               JOIN p.usuario u 
               JOIN p.categoria c 
               WHERE p.slug = :publicacion 
               AND g.isActive = :active 
               ORDER BY g.position ASC
        ')->setParameters(array('publicacion'=> $slug,'active'=>true));
        
        $publicacions=$query->getResult();
        if(isset($publicacions[0])){
            return $publicacions[0];
        }else{
            return null;
        }
    }
      
    public function queryPublicacionesActivas($publicacion_id = 0, $publicacion_slug = "",$activas=false,$conObjs = true){
        $query= $this->getEntityManager()->createQueryBuilder();
        if($conObjs){
            /*$query->select('p,c,u,g')*/
          $query->select('p,c,u')
                ->from('Richpolis\PublicacionesBundle\Entity\Publicacion', 'p')
                /*->leftJoin('p.galerias', 'g')*/
                ->leftJoin('p.usuario', 'u')
                ->leftJoin('p.categoria', 'c')    
                ->orderBy('p.createdAt', 'DESC')/*
                ->addOrderBy('g.position', 'ASC')*/; 
        }else{
            $query->select('p')
                ->from('Richpolis\PublicacionesBundle\Entity\Publicacion', 'p')
                ->orderBy('p.createdAt', 'DESC'); 
        }
        if(!$activas){
            $query->andWhere('p.isActive=:active')
                  ->setParameter('active', true);
        }
        if($publicacion_id){
            $query->andWhere('p.id=:publicacion')
                  ->setParameter('publicacion', $publicacion_id);
        }
        if(strlen($publicacion_slug)){
            $query->andWhere('p.slug=:slug')
                  ->setParameter('slug', $publicacion_slug);
        }
        return $query->getQuery();
    }
    
    public function getPublicacionesActivas($id = 0, $slug = "",$todas=false,$conObjs = true){
        $query=$this->queryPublicacionesActivas($id,$slug,$todas,$conObjs);
        return $query->getResult();
    }

    
    
    /*
     * Este query es la nueva version de getQueryPublicaciones Activas
     * 
     * @param boolean $todas solo publicaciones activas
     * @param boolean $conObjs para indicar que se necesitan todas sus relaciones.
     * @param integer|string $categoria el categoria_id|categoria_slug 
     */
    public function queryPublicaciones($todas=false,$conObjs = true,$categoria = null,$campo_order="createdAt",$order="DESC"){
        $query= $this->getEntityManager()->createQueryBuilder();
        if($conObjs){
        /*$query->select('p,c,u,g')*/
          $query->select('p,c,u')
                ->from('Richpolis\PublicacionesBundle\Entity\Publicacion', 'p')
                /*->leftJoin('p.galerias', 'g')*/
                ->leftJoin('p.usuario', 'u')
                ->leftJoin('p.categoria', 'c')    
                ->orderBy('p.'.$campo_order, $order)/*
                ->addOrderBy('g.position', 'ASC')*/; 
        }else{
            $query->select('p')
                ->from('Richpolis\PublicacionesBundle\Entity\Publicacion', 'p')
                ->orderBy('p.'.$campo_order, $order); 
        }
        if(!$todas){
            $query->andWhere('p.isActive=:active')
                  ->setParameter('active', true);
        }
        if(!is_null($categoria)){
            if(is_numeric($categoria)){
            $query->andWhere('c.id=:categoria')
                  ->setParameter('categoria', $categoria);
            }elseif(strlen($categoria)){
                $query->andWhere('c.slug=:categoria')
                      ->setParameter('categoria', $categoria);
            }
        }
        
        return $query->getQuery();
    }
    
    public function getPublicaciones($todas=false,$conObjs = true,$categoria=null,$campo_order="createdAt",$order="DESC"){
        $query=$this->queryPublicaciones($todas,$conObjs,$categoria,$campo_order,$order);
        return $query->getResult();
    }
       
    public function getRegistroUpOrDown(Publicacion $publicacion,$up=true){
        // $up = true, $up = false is down
        if($up){
            //up
            $query=$this->createQueryBuilder('p')
                    ->where('p.position<:position')
                    ->setParameter('position', $publicacion->getPosition())
                    ->orderBy('p.position', 'DESC'); //mayor a menor
        }else{
            //down
            $query=$this->createQueryBuilder('p')
                    ->where('p.position>:position')
                    ->setParameter('position', $publicacion->getPosition())
                    ->orderBy('p.position', 'ASC'); //menor a mayor
        }
        
        return $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }
    
    public function queryCarrusel($status = Publicacion::STATUS_PUBLICADO,$todos = false){
        $query=$this->getEntityManager()->createQueryBuilder();
        /*$query->select('p,c,u,g')*/
          $query->select('p,c,u')
                ->from('Richpolis\PublicacionesBundle\Entity\Publicacion', 'p')
                /*->leftJoin('p.galerias', 'g')*/
                ->leftJoin('p.usuario', 'u')
                ->leftJoin('p.categoria', 'c')
                ->where('p.isCarrusel=:isCarrusel')
                ->setParameter('isCarrusel', true)
                ->orderBy('p.isPrincipal', 'DESC')
                ->addOrderBy('p.createdAt', 'DESC');
        if($todos){
            $query->andWhere('p.status <=:status')
                  ->setParameter('status', $status);
        }else{
            $query->andWhere('p.status =:status')
                  ->setParameter('status', $status);
        }        

        return $query->getQuery();
    }
    
    public function findCarrusel($status = Publicacion::STATUS_PUBLICADO,$todos = false){
        return $this->queryCarrusel($status,$todos)->getResult();
    }
    
    public function queryPortada($status = Publicacion::STATUS_PUBLICADO,$todas = false){
        $query=$this->getEntityManager()->createQueryBuilder();
        /*$query->select('p,c,u,g')*/
          $query->select('p,c,u')
                ->from('Richpolis\PublicacionesBundle\Entity\Publicacion', 'p')
                /*->leftJoin('p.galerias', 'g')*/
                ->leftJoin('p.usuario', 'u')
                ->leftJoin('p.categoria', 'c')
                ->where('p.isPrincipal=:isPrincipal')
                ->setParameter('isPrincipal', true)
                ->orderBy('p.createdAt', 'DESC')/*
                ->addOrderBy('g.position', 'ASC')*/;
        if($todas){
            $query->andWhere('p.status <=:status')
                ->setParameter('status', $status);
        }else{
            $query->andWhere('p.status =:status')
                ->setParameter('status', $status);
        }        

        return $query->getQuery();
    }
    
    public function findPortada($status = Publicacion::STATUS_PUBLICADO, $todos = false){
        return $this->queryPortada($status,$todos)->getResult();
    }
    
    public function queryPorCategoria($categoria, $status = Publicacion::STATUS_PUBLICADO){
        $query=$this->getEntityManager()->createQueryBuilder();
        /*$query->select('p,c,u,g')*/
        $query->select('p,c,u')
                ->from('Richpolis\PublicacionesBundle\Entity\Publicacion', 'p')
                /*->leftJoin('p.galerias', 'g')*/
                ->leftJoin('p.usuario', 'u')
                ->leftJoin('p.categoria', 'c')
                ->where('p.status = :status')
                ->setParameter('status', $status)
                ->orderBy('p.createdAt', 'DESC')/*
                ->addOrderBy('g.position', 'ASC')*/;
        if(is_numeric($categoria)){
            $query->andWhere('c.id = :categoria')
                  ->setParameter('categoria', $categoria);
        }else{
            $query->andWhere('c.slug =:categoria')
                  ->setParameter('categoria', $categoria);
        }        

        return $query->getQuery();
    }
    
	public function queryBuscarPublicacion($buscar, $status = Publicacion::STATUS_PUBLICADO){
        $query=$this->getEntityManager()->createQueryBuilder();
        /*$query->select('p,c,u,g')*/
        $query->select('p,c,u')
                ->from('Richpolis\PublicacionesBundle\Entity\Publicacion', 'p')
                /*->leftJoin('p.galerias', 'g')*/
                ->leftJoin('p.usuario', 'u')
                ->leftJoin('p.categoria', 'c')
                ->where('p.status = :status')
                ->setParameter('status', $status)
                ->orderBy('p.createdAt', 'DESC')/*
                ->addOrderBy('g.position', 'ASC')*/;
        
		$query->andWhere('p.titulo LIKE :buscar')
              ->setParameter('buscar', "%".$buscar."%");
        
        return $query->getQuery();
    }
    
    /*
     * Este query es la nueva version de getQueryPublicacionesPorTipoCategoria Activas
     * 
     * @param boolean $todas solo publicaciones activas
     * @param boolean $conObjs para indicar que se necesitan todas sus relaciones.
     * @param integer|string $tipoCategoria el tipoCategoria|categoria_slug 
     */
    public function queryPublicacionesPorTipoCategoria($status = 0, $tipoCategoria = 0, 
        $conObjs = true, $campo_orden = "position" , $orden = "ASC"){

        $query= $this->getEntityManager()->createQueryBuilder();
        if($conObjs == true){
            /*$query->select('p,c,u,g')*/
              $query->select('p,c,u')   
                    ->from('Richpolis\PublicacionesBundle\Entity\Publicacion', 'p')
                    /*->leftJoin('p.galerias', 'g')*/
                    ->leftJoin('p.usuario', 'u')
                    ->leftJoin('p.categoria', 'c')    
                    ->orderBy('p.'.$campo_orden, $orden)/*
                    ->addOrderBy('g.position', 'ASC')*/;
                    
            if($tipoCategoria){
                $query->andWhere('c.tipoCategoria=:tipo')
                      ->setParameter('tipo', $tipoCategoria);
            }       
        }else{
            $query->select('p')
                    ->from('Richpolis\PublicacionesBundle\Entity\Publicacion', 'p')
                    ->orderBy('p.'.$campo_orden, $orden);
        }
        if($status){
            $query->andWhere('p.status=:status')
                  ->setParameter('status', $status);
        }
        return $query;
    }
    
    public function getPublicacionesPorTipoCategoria($status = 0, $tipoCategoria = 0, 
        $conObjs = true, $campo_orden = "position" , $orden = "ASC"){

        $query=$this->queryPublicacionesPorTipoCategoria($status,$tipoCategoria,$conObjs,$campo_orden,$orden);
        return $query->getQuery()->getResult();
    }
    
    public function getUltimasNoticias($registros = 4){

        $query=$this->queryPublicacionesPorTipoCategoria(
            0,
            CategoriaPublicacion::TIPO_CATEGORIA_NOTICIAS,
            true,
            'createdAt',
            'DESC'
        );
        return $query->getQuery()->setMaxResults($registros)->getResult();
    }
    
    public function getEventosEnInicio($registros = 4){
        $query= $this->getEntityManager()->createQueryBuilder();
        $query->select('p,c,u')   
              ->from('Richpolis\PublicacionesBundle\Entity\Publicacion', 'p')
              ->leftJoin('p.usuario', 'u')
              ->leftJoin('p.categoria', 'c')
              ->where('p.inInicio=:inicio')
              ->setParameter('inicio', true)
              ->orderBy('p.fechaEvento', 'ASC');
        $query->andWhere('c.tipoCategoria=:tipo')
              ->setParameter('tipo', CategoriaPublicacion::TIPO_CATEGORIA_EVENTOS);
        return $query->getQuery()->setMaxResults($registros)->getResult();
    }
    
    public function getUltimasPublicaciones($registros = 4){

        $query=$this->queryPublicacionesPorTipoCategoria(
            0,
            CategoriaPublicacion::TIPO_CATEGORIA_PUBLICACION,
            true,
            'createdAt',
            'DESC'
        );
        return $query->getQuery()->setMaxResults($registros)->getResult();
    }
	
    public function getPublicacionesRelacionadas(CategoriaPublicacion $categoria, $registros = 9){

        $query=$this->queryPorCategoria(
			$categoria->getId(),
            Publicacion::STATUS_PUBLICADO
        );
		
        return $query->setMaxResults($registros)->getResult();
    }

    public function queryLosmasVistosOrComentados($campo = '',$categoria = 0, $tipoCategoria = 0){
        $query=$this->getEntityManager()->createQueryBuilder();
        /*$query->select('p,c,u,g')*/
        $query->select('p,c,u')
                ->from('Richpolis\PublicacionesBundle\Entity\Publicacion', 'p')
                /*->leftJoin('p.galerias', 'g')*/
                ->leftJoin('p.usuario', 'u')
                ->leftJoin('p.categoria', 'c')
                ->orderBy('p.'.$campo, 'DESC')/*
                ->addOrderBy('g.position', 'ASC')*/;
        if($categoria){
            $query->andWhere('c.id =:categoria')
                ->setParameter('categoria', $categoria);
        }
        if($tipoCategoria){
            $query->andWhere('c.tipoCategoria =:tipoCategoria')
                ->setParameter('tipoCategoria', $tipoCategoria);
        }        

        return $query->getQuery();
    }
    
    public function findLosMasVistos($categoria = 0, $tipoCategoria = 0, $maximo = 5){
        return $this->queryLosmasVistosOrComentados('contVisitas',$categoria,$tipoCategoria)
                    ->setMaxResults($maximo)->getResult();
    }

    public function findLosMasComentados($categoria = 0, $tipoCategoria = 0, $maximo = 5){
        return $this->queryLosmasVistosOrComentados('contComentarios',$categoria,$tipoCategoria)
                    ->setMaxResults($maximo)->getResult();
    }
	

    public function findTitleSluggable($slug, $excepto = 0){
        $query= $this->getEntityManager()->createQueryBuilder();
        if($excepto > 0){
            $query->select('p')
                ->from('Richpolis\PublicacionesBundle\Entity\Publicacion', 'p')
                ->where('p.id<>:publicacion')
                ->setParameter('publicacion',$excepto)
                ->andWhere('p.slug LIKE :slug')
                ->setParameter('slug',$slug."%")
                ->orderBy('p.titulo', 'DESC'); 
        }else{
            $query->select('p')
                ->from('Richpolis\PublicacionesBundle\Entity\Publicacion', 'p')
                ->andWhere('p.slug LIKE :slug')
                ->setParameter('slug',$slug."%")
                ->orderBy('p.titulo', 'DESC'); 
        }
        return $query->getQuery()->getResult();
    }
    
    public function getEventosForFecha($year,$month,$categoria=0){
        $query= $this->getEntityManager()->createQueryBuilder();
        $query->select('p,c,u')   
              ->from('Richpolis\PublicacionesBundle\Entity\Publicacion', 'p')
              ->leftJoin('p.usuario', 'u')
              ->leftJoin('p.categoria', 'c')
              ->where('p.fechaEvento BETWEEN :inicio AND :fin ')
              ->setParameters(array(
               'inicio'=>"$year-$month-01 00:00:00",
               'fin'=>"$year-$month-31 23:59:59",   
              ))
              ->orderBy('p.fechaEvento', 'ASC');
        $query->andWhere('c.tipoCategoria=:tipo')
              ->setParameter('tipo', CategoriaPublicacion::TIPO_CATEGORIA_EVENTOS);
        if($categoria>0){
            $query->andWhere('c.id=:categoria')
                  ->setParameter('categoria', $categoria);  
        }
        return $query->getQuery()->getResult();
    }
    
    public function queryEventosEnPatrocinio(){
        $query= $this->getEntityManager()->createQueryBuilder();
        $query->select('p,c,u')   
              ->from('Richpolis\PublicacionesBundle\Entity\Publicacion', 'p')
              ->leftJoin('p.usuario', 'u')
              ->leftJoin('p.categoria', 'c')
              ->where('p.inPatrocinio=:patrocinio')
              ->setParameter('patrocinio', true)
              ->orderBy('p.fechaEvento', 'ASC');
        $query->andWhere('c.tipoCategoria=:tipo')
              ->setParameter('tipo', CategoriaPublicacion::TIPO_CATEGORIA_EVENTOS);
        return $query->getQuery();
    }
    
    public function getEventosEnPatrocinio(){
        return $this->queryEventosEnPatrocinio()->getResult();
    }
}
