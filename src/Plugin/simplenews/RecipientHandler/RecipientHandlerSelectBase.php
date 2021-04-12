<?php

namespace Drupal\simplenews\Plugin\simplenews\RecipientHandler;

/**
 * Base for Recipient Handlers that access the database directly using Select.
 *
 * Derivatives access the underlying database directly use a Select query.
 * This is very fast, but won't work with custom storage and can lead to more
 * complex queries.
 */
abstract class RecipientHandlerSelectBase extends RecipientHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function addToSpool() {
    $entity_type = $this->issue->getEntityTypeId();
    $query = $this->buildRecipientQuery();
    $query->addExpression("'$entity_type'", 'entity_type');
    $query->addExpression($this->issue->id(), 'entity_id');
    $query->addExpression(SIMPLENEWS_SUBSCRIPTION_STATUS_SUBSCRIBED, 'status');
    $query->addExpression(REQUEST_TIME, 'timestamp');
    //     *************************************************************************
    //     SN - Subscriber filters
    //     >>>>
    $query->addTag('sn1');
    //https://www.drupal.org/project/drupal/issues/3145292#comment-14054486
    //$query->execute();
    //dump($query->execute());
    //dump($query->sqlQuery->__toString());
    //exit;
    //     <<<<
    //     *************************************************************************
    $this->connection->insert('simplenews_mail_spool')->from($query)->execute();
    return $query->countQuery()->execute()->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  protected function doCount() {
    return $this->buildRecipientQuery()->countQuery()->execute()->fetchField();
  }

  /**
   * Build the query that gets the list of recipients.
   *
   * @return \Drupal\Core\Database\Query\SelectInterface
   *   Query with the columns 'snid' and 'newsletter_id' for each recipient.
   */
  abstract protected function buildRecipientQuery();

}
