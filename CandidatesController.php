<?php

namespace Crm\ApiBundle\Controller;

use Crm\ApiBundle\Service\LinkCreator;
use Crm\ApiBundle\Service\PaginationService;
use Crm\ApiBundle\Service\RequestProcessor;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Link;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Head;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;

use Doctrine\ORM;


class CandidatesController extends FOSRestController
{

    /**
     * @Get("/candidates.{_format}",
     *     name="get_candidates",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json|xml"})
     *
     * @QueryParam(name="type", nullable=true, description="type")
     * @QueryParam(name="fields", nullable=true, description="fields")
     * @QueryParam(name="filter", nullable=true, description="filtering")
     * @QueryParam(name="sort", nullable=true, description="sorting")
     * @QueryParam(name="offset", requirements="\d+", strict=true, nullable=true, description="offset")
     * @QueryParam(name="limit", requirements="\d+", strict=true, nullable=true, description="limit")
     * @param ParamFetcher $paramFetcher
     * @param RequestProcessor $requestProcessor
     * @param PaginationService $paginationService
     * @param LinkCreator $linkCreator
     * @return View
     */
    public function getCandidatesAction(
        ParamFetcher $paramFetcher,
        RequestProcessor $requestProcessor,
        PaginationService $paginationService,
        LinkCreator $linkCreator)
    {

        $query_type = $paramFetcher->get('type');
        $query_fields = $paramFetcher->get('fields');
        $query_filter = $paramFetcher->get('filter');
        $query_sort = $paramFetcher->get('sort');
        $query_offset = $paramFetcher->get('offset');
        $query_limit = $paramFetcher->get('limit');

        if ($query_type === 'count') {
            $response_array = $requestProcessor->countCollection($query_filter);
            $view = $this->view($response_array, $response_array['code']);
            $view->setHeader('Location', $linkCreator->getRequestUri());

            return $view;
        }

        $response_array = $requestProcessor->getCollection($query_fields, $query_filter, $query_sort, $query_offset, $query_limit);
        $view = $this->view($response_array, $response_array['code']);
        $view->setHeader('Location', $linkCreator->getRequestUri());
        $view->setHeader('Content-Range', $paginationService->createContentRangeHeader($query_offset, $query_limit));

        return $view;
    }


    /**
     * @Post("/candidates.{_format}",
     *     name="post_candidates",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json|xml"})
     *
     * @param RequestProcessor $requestProcessor
     * @param LinkCreator $linkCreator
     * @return View
     */
    public function postCandidatesAction(RequestProcessor $requestProcessor, LinkCreator $linkCreator)
    {
        $response_array = $requestProcessor->postCollection();
        $view = $this->view($response_array, $response_array['code']);
        $view->setHeader('Location', $linkCreator->getRequestUri());

        return $view;
    }


    /**
     * @Head("/candidates/{candidate_id}.{_format}",
     *     name="head_candidate",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json|xml", "candidate_id": "\d+"})
     *
     * @param RequestProcessor $requestProcessor
     * @param LinkCreator $linkCreator
     * @param $candidate_id
     * @return View
     */
    public function headCandidateAction(RequestProcessor $requestProcessor, LinkCreator $linkCreator, $candidate_id)
    {
        $response_array = $requestProcessor->headItem($candidate_id);
        $view = $this->view($response_array, $response_array['code']);
        $view->setHeader('Location', $linkCreator->getRequestUri());

        return $view;
    }


    /**
     * @Get("/candidates/{candidate_id}.{_format}",
     *     name="get_candidate",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json|xml", "candidate_id": "\d+"})
     *
     * @QueryParam(name="fields", nullable=true, description="fields")
     * @param ParamFetcher $paramFetcher
     * @param RequestProcessor $requestProcessor
     * @param LinkCreator $linkCreator
     * @param $candidate_id
     * @return View
     */
    public function getCandidateAction(ParamFetcher $paramFetcher, RequestProcessor $requestProcessor, LinkCreator $linkCreator, $candidate_id)
    {
        $query_fields = $paramFetcher->get('fields');

        $response_array = $requestProcessor->getItem($query_fields, $candidate_id);
        $view = $this->view($response_array, $response_array['code']);
        $view->setHeader('Location', $linkCreator->getRequestUri());
        $view->setHeader('ETag', $requestProcessor->getEtagValue());

        return $view;
    }


    /**
     * @Put("/candidates/{candidate_id}.{_format}",
     *     name="put_candidate",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json|xml", "candidate_id": "\d+"})
     *
     * @param RequestProcessor $requestProcessor
     * @param LinkCreator $linkCreator
     * @param $candidate_id
     * @return View
     */
    public function putCandidateAction(RequestProcessor $requestProcessor, LinkCreator $linkCreator, $candidate_id)
    {
        $response_array = $requestProcessor->putItem($candidate_id);
        $view = $this->view($response_array, $response_array['code']);
        $view->setHeader('Location', $linkCreator->getRequestUri());

        return $view;
    }


    /**
     * @Patch("/candidates/{candidate_id}.{_format}",
     *     name="patch_candidate",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json|xml", "candidate_id": "\d+"})
     *
     * @param RequestProcessor $requestProcessor
     * @param LinkCreator $linkCreator
     * @param $candidate_id
     * @return View
     */
    public function patchCandidateAction(RequestProcessor $requestProcessor, LinkCreator $linkCreator, $candidate_id)
    {
        $response_array = $requestProcessor->patchItem($candidate_id);
        $view = $this->view($response_array, $response_array['code']);
        $view->setHeader('Location', $linkCreator->getRequestUri());

        return $view;
    }


    /**
     * @Delete("/candidates/{candidate_id}.{_format}",
     *     name="delete_candidate",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json|xml", "candidate_id": "\d+"})
     *
     * @param RequestProcessor $requestProcessor
     * @param LinkCreator $linkCreator
     * @param $candidate_id
     * @return View
     */
    public function deleteCandidateAction(RequestProcessor $requestProcessor, LinkCreator $linkCreator, $candidate_id)
    {
        $response_array = $requestProcessor->deleteItem($candidate_id);
        $view = $this->view($response_array, $response_array['code']);
        $view->setHeader('Location', $linkCreator->getRequestUri());

        return $view;
    }
	
}
