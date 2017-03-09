<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

use Rinq\Ident\Reference;

/**
 * Revision represents a specific revision of session.
 *
 * Revision is the sole interface for manipulating a session's attribute table.
 *
 * The underlying session may be "local", i.e. owned by a peer running in this
 * process, or "remote", owned by a different peer.
 *
 * For remote sessions, operations may require network IO. Deadlines are
 * honoured for all methods that accept a context.
 */
interface Revision
{
    /**
     * @return Reference The session reference, which holds the session ID and
     *                   the revision number represented by this instance.
     */
    public function reference(): Reference;

    /**
     * Refresh returns the latest revision of the session.
     *
     * @return Revision Latest revision of the session.
     *
     * @throws NotFoundException If the session has been closed and revision is invalid.
     */
    public function refresh(Context $context): Revision;

    /**
     * Get returns the attribute with key $key from the attribute table.
     *
     * The returned attribute is guaranteed to be correct as of
     * Reference::revision().
     *
     * Non-existent attributes are equivalent to empty attributes, therefore it
     * is not an error to request a key that has never been created.
     *
     * Peers do not always have a copy of the complete attribute table. If the
     * attribute value is unknown it is fetched from the owning peer.
     *
     *  To fetch the attribute value at
     * the later revision, first call Refresh() then retry the Get() on the
     * newer revision.
     *
     * @param Context $context
     * @param string  $key
     *
     * @return Attribute
     *
     * @throws ShouldRetryException If the attribute can not be retreived because it has already been modified.
     * @throws NotFoundException    If the session has been closed and the revision can not be queried.
     */
    public function get(Context $context, string $key): Attribute;

    /**
     * GetMany the attributes with keys in k from the attribute table.
     *
     * The returned attributes are guaranteed to be correct as of Ref().Rev.
     * Non-existent attributes are equivalent to empty attributes, therefore it
     * is not an error to request keys that have never been created.
     *
     * Peers do not always have a copy of the complete attribute table. If any
     * of the attribute values are unknown they are fetched from the owning peer.
     *
     * If any of the attributes can not be retreived because they hav already
     * been modified, ShouldRetry(err) returns true. To fetch the attribute
     * values at the later revision, first call Refresh() then retry the
     * GetMany() on the newer revision.
     *
     * If IsNotFound(err) returns true, the session has been closed and the
     * revision can not be queried.
     *
     * If err is nil, t contains all of the attributes specified in k.
     */
    public function getMany(Context $context, $keys): AttributeTable;

    /**
     * Update atomically modifies the attribute table.
     *
     * A successful update produces a new revision.
     *
     * Each update is atomic; either all of the attributes in attrs are updated,
     * or the attribute table remains unchanged. On success, rev is the newly
     * created revision.
     *
     * The following conditions must be met for an update to succeed:
     *
     * 1. The session revision represented by this instance must be the latest
     *    revision. If Ref().Rev is not the latest revision the update fails;
     *    ShouldRetry(err) returns true.
     *
     * 2. All attribute changes must reference non-frozen attributes. If any of
     *    attributes being updated are already frozen the update fails and
     *    ShouldRetry(err) returns false.
     *
     * If attrs is empty no update occurs, rev is this revision and err is nil.
     *
     * As a convenience, if the update fails for any reason, rev is this
     * revision. This allows the caller to assign the return value to an
     * existing variable without first checking for errors.
     */
    public function update(Context $context, $attrs): Revision;

    /**
     * Destroy terminates the session.
     *
     * The session revision represented by this instance must be the latest
     * revision. If Ref().Rev is not the latest revision the destroy fails;
     * ShouldRetry(err) returns true.
     *
     * @throws
     */
    public function destroy(Context $context);
}
