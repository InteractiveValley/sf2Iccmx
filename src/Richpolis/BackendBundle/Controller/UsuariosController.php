<?php

namespace Richpolis\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Richpolis\BackendBundle\Entity\Usuario;
use Richpolis\BackendBundle\Form\UsuarioType;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use PHPExcel_Cell;

/**
 * Usuario controller.
 *
 * @Route("/usuarios")
 */
class UsuariosController extends Controller
{

    /**
     * Lists all Usuario entities.
     *
     * @Route("/", name="users")
     * @Method("GET")
     * @Template("BackendBundle:Usuario:index.html.twig")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackendBundle:Usuario')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    
    /**
     * Creates a new Usuario entity.
     *
     * @Route("/", name="users_create")
     * @Method("POST")
     * @Template("BackendBundle:Usuario:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Usuario();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->setSecurePassword($entity);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('users_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
    * Creates a form to create a Usuario entity.
    *
    * @param Usuario $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Usuario $entity)
    {
        $form = $this->createForm(new UsuarioType(), $entity, array(
            'action' => $this->generateUrl('users_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Usuario entity.
     *
     * @Route("/new", name="users_new")
     * @Method("GET")
     * @Template("BackendBundle:Usuario:new.html.twig")
     */
    public function newAction()
    {
        $entity = new Usuario();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
     * Finds and displays a Usuario entity.
     *
     * @Route("/{id}", name="users_show", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template("BackendBundle:Usuario:show.html.twig")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Usuario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Usuario entity.
     *
     * @Route("/{id}/edit", name="users_edit")
     * @Method("GET")
     * @Template("BackendBundle:Usuario:edit.html.twig")
     */
    public function editAction($id) 
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Usuario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm)
        );
    }

    /**
    * Creates a form to edit a Usuario entity.
    *
    * @param Usuario $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Usuario $entity)
    {
        $form = $this->createForm(new UsuarioType(), $entity, array(
            'action' => $this->generateUrl('users_update', array('id' => $entity->getId())),
            'method' => 'PUT'
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Usuario entity.
     *
     * @Route("/{id}", name="users_update")
     * @Method("PUT")
     * @Template("BackendBundle:Usuario:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Usuario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        
        //obtiene la contraseÃ±a actual -----------------------
        $current_pass = $entity->getPassword();

        if ($editForm->isValid()) {
            if (null == $entity->getPassword()) {
                // La tienda no cambia su contraseÃ±a, utilizar la original
                $entity->setPassword($current_pass);
            } else {
                // actualizamos la contraseÃ±a
                $this->setSecurePassword($entity);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('users_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm)
        );
    }
    /**
     * Deletes a Usuario entity.
     *
     * @Route("/{id}", name="users_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        //if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Usuario')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Usuario entity.');
            }

            $em->remove($entity);
            $em->flush();
        //}

        return $this->redirect($this->generateUrl('users'));
    }

    /**
     * Creates a form to delete a Usuario entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('users_delete', array('id' => $id)))
            ->setMethod('DELETE')
            /*->add('submit', 'submit', array(
                'label' => 'Eliminar',
                'attr'=>array(
                    'class'=>'btn btn-danger'
            )))*/
            ->getForm()
        ;
    }
    
    
    
    private function setSecurePassword(&$entity) {
        // encoder
        $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
        $passwordCodificado = $encoder->encodePassword(
                    $entity->getPassword(),
                    $entity->getSalt()
        );
        $entity->setPassword($passwordCodificado);
    }
    
    /**
     * Exporta la lista completa de usuarios.
     *
     * @Route("/exportar", name="users_export")
     * @Method("GET")
     */
    public function exportarAction() {
        $usuarios = $this->getDoctrine()
                ->getRepository('BackendBundle:Usuario')
                ->findBy(array('newsletter'=>true));

        $response = $this->render(
                'BackendBundle:Usuario:list.xls.twig', array('usuarios' => $usuarios)
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="export-newsletter.xls"');
        return $response;
    }
	
	/**
     * Importa lista de usuarios nuevos.
     *
     * @Route("/importar", name="users_import")
     * @Method({"GET","POST"})
     */
	public function importarAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'POST') {
            $archivo = $request->files->get('archivo');
            if ($archivo instanceof UploadedFile) {
                $uploads = $this->container->getParameter('richpolis.uploads');
                if(!file_exists($uploads)){
                    mkdir($uploads, 0777);
                }
                $fileName = $uploads . '/' . $archivo->getClientOriginalName();
                if(file_exists($fileName)){
                    unlink($fileName);
                }
                $archivo->move(
                    $uploads, $archivo->getClientOriginalName()
                );
                $this->importar($fileName);
            } else {
                print_r("Error al subir archivo");
                die();
            }
            return $this->redirect($this->generateUrl('users'));  
        }
        return $this->render('BackendBundle:Usuario:importar.html.twig');
    }

    private function importar($filename) {
        //cargamos el archivo a procesar.
        $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject($filename);
        //se obtienen las hojas, el nombre de las hojas y se pone activa la primera hoja
        $total_sheets = $objPHPExcel->getSheetCount();
        $allSheetName = $objPHPExcel->getSheetNames();
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
        //Se obtiene el número máximo de filas
        $highestRow = $objWorksheet->getHighestRow();
        //Se obtiene el número máximo de columnas
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        //$headingsArray contiene las cabeceras de la hoja excel. Llos titulos de columnas
        $headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, true, true);
        $headingsArray = $headingsArray[1];

        //Se recorre toda la hoja excel desde la fila 2 y se almacenan los datos
        $r = -1;
        $namedDataArray = array();
        for ($row = 2; $row <= $highestRow; ++$row) {
            $dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, true, true);
            if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')) {
                ++$r;
                foreach ($headingsArray as $columnKey => $columnHeading) {
                    $namedDataArray[$r][$columnHeading] = $dataRow[$row][$columnKey];
                } //endforeach
            } //endif
        }
        $this->loadToDB($namedDataArray);
    }
    
    private function loadToDB($registros = null) {
        $em = $this->getDoctrine()->getManager();
        if ($registros && count($registros) > 1) {
            foreach ($registros as $registro) {
                $usuario = new Usuario();
				$usuario->setUsername($registro['username']);
 				$usuario->setEmail($registro['password'].'@'.$registro['username'].'.iccmx.mx');
				$usuario->setPassword($registro['password']);
				$usuario->setNombre($registro['username']);
				$usuario->setGrupo(Usuario::GRUPO_USUARIOS);
				$this->setSecurePassword($usuario);
				$em->persist($usuario);
            }
			$em->flush();
        }
    }
  
	
	
}
