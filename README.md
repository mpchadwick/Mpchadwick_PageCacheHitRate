#Mpchadwick_PageCacheHitRate

Hit rate tracking for Magento Page Cache.

###Configuration

All configuration is done through an XML file in the `/app/etc` directory (e.g. `enterprise.xml`). This is because module configuration is not loaded in the case of a page cache hit. You will need to...

- Set `Mpchadwick_PageCacheHitRate_Model_Processor` as your cache `<request_processor>`.
- Set a `<tracker>` in your `<full_page_cache>` configuration.

Example configuration...

```xml
<?xml version='1.0' encoding="utf-8" ?>
<config>
    <global>
        <cache>
            <request_processors>
                <ee>Mpchadwick_PageCacheHitRate_Model_Processor</ee>
            </request_processors>
            <frontend_options>
                <slab_size>1040000</slab_size>
            </frontend_options>
        </cache>
        <full_page_cache>
            <backend>Mage_Cache_Backend_File</backend>
            <backend_options>
                <cache_dir>full_page_cache</cache_dir>
            </backend_options>
            <tracker>Mpchadwick_PageCacheHitRate_Model_Tracker_File</tracker>
        </full_page_cache>
        <skip_process_modules_updates>0</skip_process_modules_updates>
    </global>
</config>
```

###Trackers

The following trackers are available...

- **`Mpchadwick_PageCacheHitRate_Model_Tracker_File`** A log file will be used for for logging hits / misses. A new file will be created each day.
- **`Mpchadwick_PageCacheHitRate_Model_Tracker_NewRelic`** Hits and misses will be tracked as [New Relic custom events](https://docs.newrelic.com/docs/insights/new-relic-insights/adding-querying-data/inserting-custom-events-new-relic-apm-agents). [NOTE: untested].

You can easily create your own tracker if you'd prefer a differrent means of tracking. Simply implement the `Mpchadwick_PageCacheHitRate_Model_TrackerInterface` interface and configure your class as the `<tracker>` in an xml file in `/app/etc`.

###Metrics

In addition to just tracking for hit / miss, route, URL and IP addresses are also tracked for each request. For example, you may want to see your cache hit rate for a single route such as `/catalog/product/view` or know your cache hit rate for a single IP address if your are seeing crawling / bot activity creating performance impact on your site.

