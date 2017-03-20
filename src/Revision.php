<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

use Rinq\Exception\FrozenAttributeException;
use Rinq\Exception\StaleFetchException;
use Rinq\Exception\StaleUpdateException;
use Rinq\Ident\Reference;

/**
 * Revision represents a specific revision of session.
 *
 * Revision is the sole interface for manipulating a session's attribute table.
 *
 * The underlying session may be "local", i.e. owned by a peer running in this
 * process, or "remote", owned by a different peer.
 *
 * For remote sessions, operations may require network IO. Timeouts are
 * honoured for all methods that accept a context.
 */
interface Revision
{
    /**
     * The session reference, which holds the session ID and the revision number
     * represented by this instance.
     */
    public function reference(): Reference;

    /**
     * Refresh returns the latest revision of the session.
     *
     * @throws NotFoundException If the session has been closed and revision is invalid.
     */
    public function refresh(Context $context): Revision;

    /**
     * Get returns the attribute with key $key from the attribute table.
     *
     * The returned attribute is guaranteed to be correct as of
     * $this->reference->revision().
     *
     * Non-existent attributes are equivalent to empty attributes, therefore it
     * is not an error to request a key that has never been created.
     *
     * Peers do not always have a copy of the complete attribute table. If the
     * attribute value is unknown it is fetched from the owning peer.
     *
     * If any of the attributes can not be retrieved because they have already
     * been modified, StaleFetchException will be thrown. To fetch the attribute
     * value at the later revision, first call refresh() then retry the get() on
     * the newer revision.
     *
     * @return Attribute The attribute matching key $key.
     *
     * @throws StaleFetchException If the attribute can not be retrieved because it has already been modified.
     * @throws NotFoundException   If the session has been closed and the revision can not be queried.
     */
    public function get(Context $context, string $key): Attribute;

    /**
     * GetMany returns the attributes with keys in $keys from the attribute
     * table.
     *
     * The returned attributes are guaranteed to be correct as of
     * $this->reference->revision().
     *
     * Non-existent attributes are equivalent to empty attributes, therefore it
     * is not an error to request keys that have never been created.
     *
     * Peers do not always have a copy of the complete attribute table. If any
     * of the attribute values are unknown they are fetched from the owning peer.
     *
     * If any of the attributes can not be retrieved because they have already
     * been modified, StaleFetchException will be thrown. To fetch the attribute
     * values at the later revision, first call refresh() then retry the
     * getMany() on the newer revision.
     *
     * @param array<string> A list of keys.
     *
     * @return AttributeTable With the the keys $keys.
     *
     * @throws StaleFetchException If the attribute can not be retrieved because it has already been modified.
     * @throws NotFoundException   If the session has been closed and the revision can not be queried.
     */
    public function getMany(Context $context, array $keys): AttributeTable;

    /**
     * Update atomically modifies the attribute table.
     *
     * A successful update produces a new revision.
     *
     * Each update is atomic; either all of the attributes in attrs are updated,
     * or the attribute table remains unchanged.
     *
     * The following conditions must be met for an update to succeed:
     *
     * 1. The session revision represented by this instance must be the latest
     *    revision. If $this->reference->revision() is not the latest revision
     *    the update fails; and StaleUpdateException is thrown.
     *
     * 2. All attribute changes must reference non-frozen attributes. If any of
     *    attributes being updated are already frozen the update fails; and
     *    FrozenAttributeException is thrown.
     *
     * @throws FrozenAttributeException When updating frozen attributes.
     * @throws StaleUpdateException     When updating any but the latest revision.
     * @throws NotFoundException        If the session has been closed and the revision can not be queried.
     */
    public function update(Context $context, $attrs): Revision;

    /**
     * Destroy terminates the session.
     *
     * The session revision represented by this instance must be the latest
     * revision. If $this->reference()->revision() is not the latest revision
     * the destroy fails; and StaleUpdateException is thrown.
     *
     * @throws StaleUpdateException When updating any but the latest revision.
     * @throws NotFoundException    If the session has been closed and the revision can not be queried.
     */
    public function destroy(Context $context): void;
}
